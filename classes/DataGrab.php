<?php

date_default_timezone_set('Europe/Stockholm');

use WebSocket\Client;

class SaunaStatistics
{
    public $bathersToday;

    public $batherslastHour;

    public $bathersRemaining;

    private $last;

    private $queryWs;

    protected $client;

    public $stats;

    public function __construct()
    {

        $this->stats->visitors->bathersToday = 0;
        $this->stats->visitors->batherslastHour = 0;
        $this->stats->visitors->bathersRemaining = 0;
        $this->last = 0;

        $this->client = new Client(
            'wss://www.mittsaltsjobad.se/bjerred-weather-widget/websocket',
            [
                'timeout' => 30,
                'origin' => 'https://www.mittsaltsjobad.se',
                'ssl' => true,
                'persistent' => true,
            ]
        );

    }

    // Prepare query for websocket
    public function prepareQuery(string $topic, array $payload)
    {
        $this->queryWs = json_encode([
            'topic' => $topic,
            'payload' => $payload,
            'userID' => rand(1000, 80000),
        ]);

        return $this->queryWs;

    }

    public function calculateBatherStatistics()
    {

        // Prepare the query for the websocket
        $this->prepareQuery(
            'readDatabase',
            [
                'device' => [
                    'deviceType' => 'blinky-turnstile',
                    'name' => '01',
                    'attr' => [
                        'numTurns',
                    ],
                    'startDate' => strtotime('today 00:00') * 1000, // Epoch milliseconds
                    'stopDate' => strtotime('today 23:59') * 1000, // Epoch milliseconds
                ],
            ]
        );

        // Initiate the websocket connection and caclulate the numbers
        try {
            $this->client->send($this->queryWs);

            $turnStiles = json_decode($this->client->receive());

            if ($turnStiles === null) {
                throw new Exception('No turnstiles data');
            }

            // Do the math
            foreach ($turnStiles->payload->trace as $rotation) {
                if ($rotation->numTurns > $this->last) {
                    $this->stats->visitors->bathersToday++;
                    if ($rotation->timeStamp > strtotime('1 hour ago') * 1000) {
                        $this->stats->visitors->batherslastHour++;
                    }
                }
                $this->last = $rotation->numTurns;
            }

            // Get remaining bathers
            // $lastnumTurns = end($turnStiles->payload->trace)->numTurns;
            $this->stats->visitors->bathersRemaining = end($turnStiles->payload->trace)->numTurns;

        } catch (\Throwable $th) {
            print_r($th);
            exit();
        }
    }

    public function getWeatherStatistics()
    {

        // Prepare the query for the websocket
        $this->prepareQuery(
            'getDevices',
            []
        );

        // Initiate the websocket connection and caclulate the numbers
        try {
            $this->client->send($this->queryWs);

            $weatherData = json_decode($this->client->receive());

            if ($weatherData === null) {
                throw new Exception('No turnstiles data');
            }

            // Get the statistics together

            // Weatherstation data
            $this->stats->weather->wind->speedUnit = $weatherData->payload[2]->gusts->unit;
            $this->stats->weather->wind->directionUnit = $weatherData->payload[2]->windDirection->unit;
            $this->stats->weather->wind->windSpeed1 = $weatherData->payload[2]->windSpeed1->value;
            $this->stats->weather->wind->windSpeed2 = $weatherData->payload[2]->windSpeed2->value;
            $this->stats->weather->wind->gusts = $weatherData->payload[2]->gusts->value;
            $this->stats->weather->wind->direction = $weatherData->payload[2]->windDirection->value;
            $this->stats->weather->temp->temp = round($weatherData->payload[2]->temperature->value, 1);

            // Temperature data
            $this->stats->weather->temp->tempUnit = $weatherData->payload[0]->tempA->unit;
            $this->stats->weather->temp->water = round($weatherData->payload[0]->tempB->value, 1);
            $this->stats->weather->temp->air = round($weatherData->payload[0]->tempA->value, 1);

        } catch (\Throwable $th) {
            print_r($th);
            exit();
        }
    }
}
