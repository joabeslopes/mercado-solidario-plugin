<?php

namespace Mercado_Solidario\REST;
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

        new Stock();
        new Families();
    }

};