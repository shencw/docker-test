<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7ab5f01b98982899f5383b91156070a4
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SwoStar\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SwoStar\\' => 
        array (
            0 => __DIR__ . '/..' . '/wei/swostar/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7ab5f01b98982899f5383b91156070a4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7ab5f01b98982899f5383b91156070a4::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
