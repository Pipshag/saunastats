<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

require 'vendor/autoload.php';
require 'classes/DataGrab.php';

// Load the `DataGrab` class for Sauna Stats
// this also loads the `WebSocket\Client` class
$sauna = new SaunaStatistics();

// Calculate Visitorstats
$sauna->calculateBatherStatistics();
// Retrieve environmental data
$sauna->getWeatherStatistics();

if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode($sauna->stats);
    exit;
}

// Initialize mustache template engine and set options
$mustache = new Mustache_Engine([
    'entity_flags' => ENT_QUOTES,
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
]);

// Output mustache template and render it with data
echo $mustache->render(
    'home',
    [
        'visitors' => $sauna->stats->visitors,
        'weather' => $sauna->stats->weather,
    ]
);
