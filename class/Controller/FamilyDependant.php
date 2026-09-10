<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Base;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class FamilyDependant extends Base\Controller {
    public static string $post_type = MERCADO_SOLIDARIO_POST_PREFIX.'family_dependant';

    public function __construct() {
        $this->model = new Model\FamilyDependant();
        add_action('init', [$this, 'register_post_type']);
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_post_type() {
        register_post_type(
            self::$post_type,
            [
                'labels' => [
                    'name' => 'Dependentes da Família',
                    'singular_name' => 'Dependente da Família',
                ],
                'public' => false,
                'show_ui' => false,
                'show_in_menu' => false,
                'rewrite' => false
            ]
        );
    }

    public function register_routes() {
        $base_route = self::get_base_route(Families::class);

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            '/' . $base_route . '/(?P<family_id>\d+)/dependants',
            [
                'methods' => 'GET',
                'callback' => [ $this->model, 'get' ],
                'permission_callback' => [$this, 'get_permission']
            ]
        );

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            '/' . $base_route . '/(?P<family_id>\d+)/dependants',
            [
                'methods' => 'POST',
                'callback' => [ $this->model, 'post' ],
                'permission_callback' => [$this, 'get_permission']
            ]
        );

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            '/' . $base_route . '/(?P<family_id>\d+)/dependants/(?P<dependant_id>\d+)',
            [
                [
                    'methods' => 'GET',
                    'callback' => [ $this->model, 'get' ],
                    'permission_callback' => [$this, 'get_permission']
                ],
                [
                    'methods' => 'PUT',
                    'callback' => [ $this->model, 'put' ],
                    'permission_callback' => [$this, 'get_permission']
                ],
                [
                    'methods' => 'DELETE',
                    'callback' => [ $this->model, 'delete' ],
                    'permission_callback' => [$this, 'get_permission']
                ]
            ]
        );
    }
}
