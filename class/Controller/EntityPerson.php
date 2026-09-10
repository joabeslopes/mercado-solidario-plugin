<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Base;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class EntityPerson extends Base\Controller {
    public static string $post_type = MERCADO_SOLIDARIO_POST_PREFIX.'entity_person';
    public function __construct() {

        $this->model = new Model\EntityPerson();
        add_action('init', [$this, 'register_post_type']);

        add_action('rest_api_init', [$this, 'register_person_relations']);
    }

    public function register_post_type() {
        register_post_type(
            self::$post_type,
            [
                'labels' => [
                    'name' => 'Relações Pessoa-Entidade',
                    'singular_name' => 'Relação Pessoa-Entidade',
                ],
                'public' => false,
                'show_ui' => false,
                'show_in_menu' => false,
                'rewrite' => false
            ]
        );
    }

    public function register_person_relations() {
        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            '/persons/(?P<person_id>\d+)/entities',
            [
                'methods' => 'GET',
                'callback' => [ $this->model, 'get' ],
                'permission_callback' => [$this, 'get_permission']
            ]
        );

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            '/persons/(?P<person_id>\d+)/entities',
            [
                'methods' => 'POST',
                'callback' => [ $this->model, 'post' ],
                'permission_callback' => [$this, 'get_permission']
            ]
        );

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            '/persons/(?P<person_id>\d+)/entities/(?P<entity_id>\d+)',
            [
                'methods' => 'PUT',
                'callback' => [ $this->model, 'put' ],
                'permission_callback' => [$this, 'get_permission']
            ]
        );

        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            '/persons/(?P<person_id>\d+)/entities/(?P<entity_id>\d+)',
            [
                'methods' => 'DELETE',
                'callback' => [ $this->model, 'delete' ],
                'permission_callback' => [$this, 'get_permission']
            ]
        );
    }

}
