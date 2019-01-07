<?php

// autoload_commands_real.php @generated by Composer

class ComposerAutoloaderInit053b1c91444574fd6eb42823434cae79
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit053b1c91444574fd6eb42823434cae79', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit053b1c91444574fd6eb42823434cae79', 'loadClassLoader'));

        $classMap = require __DIR__ . '/autoload_commands_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }
        $loader->register(true);

        return $loader;
    }
}
