<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitda1505d38a3cb21e67ab036849b059fb
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitda1505d38a3cb21e67ab036849b059fb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitda1505d38a3cb21e67ab036849b059fb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitda1505d38a3cb21e67ab036849b059fb::$classMap;

        }, null, ClassLoader::class);
    }
}