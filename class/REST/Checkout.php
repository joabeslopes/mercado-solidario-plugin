<?php

namespace Mercado_Solidario\REST;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkout {

    public string $base_route = 'checkout';
    public Model\Checkout $model;

    public function __construct(){

        $this->model = new Model\Checkout();

        add_action( 'rest_api_init', [ $this, 'register_get_stock' ] );
        add_action( 'rest_api_init', [ $this, 'register_post_cart' ] );

    }

    public function register_get_stock() {

        register_rest_route( 
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route.'/stock', 
            [
            'methods' => 'GET',
            'callback' => [ $this->model, 'get_stock' ],
            'permission_callback' => [ $this->model, 'checkout_permission' ],
            ]
        );

    }

    public function register_post_cart() {

        register_rest_route( 
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route.'/cart', 
            [
            'methods' => 'POST',
            'callback' => [ $this->model, 'post_cart' ],
            'permission_callback' => [ $this->model, 'checkout_permission' ],
            ]
        );

    }

}