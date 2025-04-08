<?php

namespace Mercado_Solidario\REST;
use Mercado_Solidario\Controller;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Stock {

    public string $base_route = 'stock';
    public Controller\Stock $controller;

    public function __construct(){

        $this->controller = new Controller\Stock();

        add_action( 'rest_api_init', [ $this, 'register_get' ] );

    }

    public function register_get() {
        
        register_rest_route( 
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route, 
            [
            'methods' => 'GET',
            'callback' => [ $this->controller, 'get_stock' ],
            'permission_callback' => [ $this->controller, 'get_stock_permission' ],
            ]
        );

    }

}