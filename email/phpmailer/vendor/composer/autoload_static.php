<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite5fadbc8d81e2994888caff5778f0107
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
            $loader->prefixLengthsPsr4 = ComposerStaticInite5fadbc8d81e2994888caff5778f0107::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite5fadbc8d81e2994888caff5778f0107::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite5fadbc8d81e2994888caff5778f0107::$classMap;

        }, null, ClassLoader::class);
    }
}
