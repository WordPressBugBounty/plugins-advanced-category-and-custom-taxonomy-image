<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb5f525427875397b7284f593175f93f7
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Cache\\' => 10,
        ),
        'D' => 
        array (
            'Detection\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/cache/src',
        ),
        'Detection\\' => 
        array (
            0 => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb5f525427875397b7284f593175f93f7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb5f525427875397b7284f593175f93f7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb5f525427875397b7284f593175f93f7::$classMap;

        }, null, ClassLoader::class);
    }
}
