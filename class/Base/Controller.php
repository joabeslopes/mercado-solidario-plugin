<?php

namespace Mercado_Solidario\Base;
use Mercado_Solidario\Security\CapabilitiesManager;
use ReflectionClass;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Controller {

    public static string $post_type;
    public object $model;

    public function get_permission(){
        return current_user_can( CapabilitiesManager::getFromClass($this) );
    }

    public function register(string $method){
        add_action( 'rest_api_init', [ $this, $method ] );
    }

    private function set_route($method){
        register_rest_route(
            MERCADO_SOLIDARIO_REST_NAMESPACE,
            self::get_base_route($this),
            [
            'methods' => strtoupper($method),
            'callback' => [ $this->model, $method ],
            'permission_callback' => [ $this, 'get_permission' ],
            ]
        );
    }

    public static function get_base_route($class):string{
        $ref = new ReflectionClass($class);

        if (!$ref){
            return '';
        };

        $className = $ref->getShortName();

        return strtolower($className);
    }

    public function get() {
        $this->set_route('get');
    }

    public function post() {
        $this->set_route('post');
    }

    public function delete() {
        $this->set_route('delete');
    }

    public function put() {
        $this->set_route('put');
    }

}