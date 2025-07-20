<?php

namespace Mercado_Solidario\REST;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families {

    public string $base_route = 'families';
    public Model\Families $model;

    public function __construct(){

        $this->model = new Model\Families();
        add_action( 'rest_api_init', [ $this, 'register_get' ] );
        add_action( 'rest_api_init', [ $this, 'register_post' ] );
    }

    public function register_get() {

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route,
            [
            'methods' => 'GET',
            'callback' => [ $this->model, 'get_all_families' ],
            'permission_callback' => [ $this->model, 'get_permission' ],
            ]
        );

    }

    public function register_post() {

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            $this->base_route,
            [
            'methods' => 'POST',
            'callback' => [ $this->model, 'post_family' ],
            'permission_callback' => [ $this->model, 'get_permission' ],
            ]
        );

    }

}