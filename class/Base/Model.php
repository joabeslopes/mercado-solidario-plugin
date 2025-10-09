<?php

namespace Mercado_Solidario\Base;
use WP_REST_Request;
use WP_REST_Response;
use WP_Post;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Model {

    public function success_response( $data=true ): WP_REST_Response {
        return new WP_REST_Response(
            [ 'data' => $data ],
            200
        );
    }

    public function error_response( $data='' ): WP_REST_Response {
        return new WP_REST_Response(
            [ 'data' => $data ],
            500
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

};