<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite1abbfae6e1aed40c4f975aab5270f47
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DiviSquad\\Utils\\' => 16,
            'DiviSquad\\Modules\\' => 18,
            'DiviSquad\\Manager\\' => 18,
            'DiviSquad\\Integration\\' => 22,
            'DiviSquad\\Base\\' => 15,
            'DiviSquad\\Admin\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DiviSquad\\Utils\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/utils',
        ),
        'DiviSquad\\Modules\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/modules',
        ),
        'DiviSquad\\Manager\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/manager',
        ),
        'DiviSquad\\Integration\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/integration',
        ),
        'DiviSquad\\Base\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/base',
        ),
        'DiviSquad\\Admin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/admin',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite1abbfae6e1aed40c4f975aab5270f47::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite1abbfae6e1aed40c4f975aab5270f47::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite1abbfae6e1aed40c4f975aab5270f47::$classMap;

        }, null, ClassLoader::class);
    }
}
