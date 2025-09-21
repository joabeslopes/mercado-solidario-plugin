<?php

namespace Mercado_Solidario\Controller;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Base {

    public string $base_route = '';
    public object $model;

    public function get_permission(){
        return current_user_can( MERCADO_SOLIDARIO_CAPABILITY );
    }

    public function register(string $method){
        add_action( 'rest_api_init', [ $this, strtolower($method) ] );
    }

    public function get() {

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route,
            [
            'methods' => 'GET',
            'callback' => [ $this->model, 'get_all' ],
            'permission_callback' => [ $this, 'get_permission' ],
            ]
        );
    }

    public function post() {

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route,
            [
            'methods' => 'POST',
            'callback' => [ $this->model, 'post' ],
            'permission_callback' => [ $this, 'get_permission' ],
            ]
        );
    }

    public function delete() {

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route,
            [
            'methods' => 'DELETE',
            'callback' => [ $this->model, 'delete' ],
            'permission_callback' => [ $this, 'get_permission' ],
            ]
        );
    }

}