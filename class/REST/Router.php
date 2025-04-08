<?php

namespace Mercado_Solidario\REST;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Router{

    public function __construct(){

        new Stock();

        new People();

    }

}