<?php

namespace Mercado_Solidario\Base;
use WP_REST_Request;
use WP_REST_Response;
use WP_Post;
use ReflectionClass;
use ReflectionProperty;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Model {

    public function success_response( $data=true, $status=200 ): WP_REST_Response {
        return new WP_REST_Response(
            [ 'data' => $data ],
            $status
        );
    }

    public function error_response( $data='', $status=500 ): WP_REST_Response {
        return new WP_REST_Response(
            [ 'data' => $data ],
            $status
        );
    }
    public static function build_from_post(WP_Post $post){
    }
    public function get(WP_REST_Request $request): WP_REST_Response {
    }
    public function post(WP_REST_Request $request): WP_REST_Response {
    }
    public function delete(WP_REST_Request $request): WP_REST_Response  {
    }
    public function put(WP_REST_Request $request): WP_REST_Response  {
    }

    public function get_attributes(): array {
        $attributes = [];
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        };
        return $attributes;
    }
};