<?php

namespace Mercado_Solidario\REST;
use ReflectionClass;
use WP_Error;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Router{

    public static function success_response( $data=true ): array{
        return [ 'data' => $data ];
    }

    public static function error_response($code='', $data=''): WP_Error{
        return new WP_Error(code: $code, data: $data);
    }

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