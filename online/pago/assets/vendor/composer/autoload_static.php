<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit00fa6c85abb9a682ac64dd8396f317ae
{
    public static $files = array (
        'cf150f72bd303a2ff07711c9a70f2d53' => __DIR__ . '/..' . '/google/apiclient/src/Google/autoload.php',
    );

    public static $prefixLengthsPsr4 = array (
        'r' => 
        array (
            'rapidweb\\googlecontacts\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'rapidweb\\googlecontacts\\' => 
        array (
            0 => __DIR__ . '/..' . '/rapidwebltd/php-google-contacts-v3-api',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit00fa6c85abb9a682ac64dd8396f317ae::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit00fa6c85abb9a682ac64dd8396f317ae::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
