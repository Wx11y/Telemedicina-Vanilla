<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitda185735919ad83f7223fb3347e38ccc
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitda185735919ad83f7223fb3347e38ccc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitda185735919ad83f7223fb3347e38ccc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitda185735919ad83f7223fb3347e38ccc::$classMap;

        }, null, ClassLoader::class);
    }
}
