<?php

namespace Mercado_Solidario\REST;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Router{

    public function __construct(){

        new Checkout();

        new Families();

    }

}