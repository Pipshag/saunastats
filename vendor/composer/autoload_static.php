<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdf1f81a0630f57ab5fb14da5bc0832c6
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WebSocket\\' => 10,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WebSocket\\' => 
        array (
            0 => __DIR__ . '/..' . '/textalk/websocket/lib',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'Mustache' => 
            array (
                0 => __DIR__ . '/..' . '/mustache/mustache/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdf1f81a0630f57ab5fb14da5bc0832c6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdf1f81a0630f57ab5fb14da5bc0832c6::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitdf1f81a0630f57ab5fb14da5bc0832c6::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitdf1f81a0630f57ab5fb14da5bc0832c6::$classMap;

        }, null, ClassLoader::class);
    }
}
