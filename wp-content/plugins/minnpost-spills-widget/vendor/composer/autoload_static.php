<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite829550e45f0815d556b051e4a655a84
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
        'B' => 
        array (
            'Brain\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
        'Brain\\' => 
        array (
            0 => __DIR__ . '/..' . '/brain/cortex/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite829550e45f0815d556b051e4a655a84::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite829550e45f0815d556b051e4a655a84::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
