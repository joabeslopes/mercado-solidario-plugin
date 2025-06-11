<?php

namespace Mercado_Solidario\REST;
use Mercado_Solidario\Model;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Checkout {

    public string $base_route = 'checkout';
    public Model\Checkout $model;

    public function __construct(){

        $this->model = new Model\Checkout();

        add_action( 'rest_api_init', [ $this, 'register_get' ] );

    }

    public function register_get() {
        
        register_rest_route( 
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route.'/stock', 
            [
            'methods' => 'GET',
            'callback' => [ $this->model, 'get_stock' ],
            'permission_callback' => [ $this->model, 'get_stock_permission' ],
            ]
        );

    }

}