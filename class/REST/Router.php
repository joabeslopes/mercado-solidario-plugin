<?php

namespace Mercado_Solidario\REST;
use WP_Error;

// don't call the file directly
defined( 'ABSPATH' ) || die;

function success_response( $data ): array{
    return [ 'data' => $data ];
};

function error_response($code='', $data=''): WP_Error{
    return new WP_Error(code: $code, data: $data);
}

class Router{

    public function __construct(){

        new Stock();
        new Families();
    }

};