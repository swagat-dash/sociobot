<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite34fc3eea6eab22f47f212ff1f609008
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInite34fc3eea6eab22f47f212ff1f609008::$classMap;

        }, null, ClassLoader::class);
    }
}
