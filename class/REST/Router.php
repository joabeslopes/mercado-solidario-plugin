<?php

namespace Mercado_Solidario\REST;
use ReflectionClass;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Router{
    public function __construct(){

        $dir = MERCADO_SOLIDARIO_DIR . 'class/Controller';
        $namespace = 'Mercado_Solidario\\Controller\\';

        foreach (scandir($dir) as $file) {
            if (substr($file, -4) === '.php') {
                $class = $namespace . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    if ($reflection->isInstantiable()) {
                        $reflection->newInstance();
                    };
                };
            };
        };
    }

};