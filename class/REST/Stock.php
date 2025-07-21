<?php

namespace Mercado_Solidario\REST;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Stock {

    public string $base_route = 'stock';
    public Model\Stock $model;

    public function __construct(){

        $this->model = new Model\Stock();

        add_action( 'rest_api_init', [ $this, 'register_get_stock' ] );
        add_action( 'rest_api_init', [ $this, 'register_post_checkout' ] );
        add_action( 'rest_api_init', [ $this, 'register_post_checkin' ] );
    }

    public function register_get_stock() {

        register_rest_route( 
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route, 
            [
            'methods' => 'GET',
            'callback' => [ $this->model, 'get_stock' ],
            'permission_callback' => [ $this->model, 'get_permission' ],
            ]
        );

    }

    public function register_post_checkout() {

        register_rest_route( 
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route.'/checkout', 
            [
            'methods' => 'POST',
            'callback' => [ $this->model, 'post_checkout' ],
            'permission_callback' => [ $this->model, 'get_permission' ],
            ]
        );

    }

    public function register_post_checkin() {

        register_rest_route( 
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route.'/checkin', 
            [
            'methods' => 'POST',
            'callback' => [ $this->model, 'post_checkin' ],
            'permission_callback' => [ $this->model, 'get_permission' ],
            ]
        );

    }

}