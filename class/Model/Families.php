<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\REST\Router;
use WP_Error;
use WP_REST_Request;
use WP_Query;
use WP_Post;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families {

    public function __construct(){
        add_action('init', [$this, 'register_family_post_type']);
    }

    public function register_family_post_type(){
        register_post_type(MERCADO_SOLIDARIO_FAMILY_POST, [
            'public' => false
        ]);
    }

    private function search_family(WP_Query $query): ?array {

        if ($query->have_posts()) {
            $result = [];
            $posts = $query->get_posts();
            foreach ($posts as $post) {
                $result[] = (array) Family::build_from_post($post);
            };

            return $result;

        } else {
            return null;
        };
    }

    public function get_all_families() {

        $today = current_time('Y-m-d');

        $query = new WP_Query([
            'post_type'      => MERCADO_SOLIDARIO_FAMILY_POST,
            'posts_per_page' => -1, // All results
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => 'valid_until',
                    'value'   => $today,
                    'compare' => '>='
                ]
            ]
        ]);

        $families = $this->search_family($query);

        if ($families) {
            return Router::success_response($families);
        } else {
            return Router::error_response('error', 'Nenhuma familia encontrada');
        };
    }

    public function post_family( WP_REST_Request $request ){
        $new_family = $request['newFamily'];

        if(!$new_family){
            return Router::error_response('erro', 'Faltou enviar a nova familia');
        };

        $family = new Family(
            name: $new_family['name'],
            cpf: $new_family['cpf'],
            phone: $new_family['phone'],
            balance: $new_family['balance'],
            valid_until: $new_family['valid_until'],
            notes: $new_family['notes']
        );

        $search = $this->search_by_cpf( $family->cpf );
        if (is_array($search)){
            return Router::error_response('erro', 'Família já cadastrada');
        };

        $search = $this->search_by_phone( $family->phone );
        if (is_array($search)){
            return Router::error_response('erro', 'Família já cadastrada');
        };

        if ( $family->save() ){
            return Router::success_response($family);
        } else {
            return Router::error_response('erro', 'Não foi possível salvar');
        };
    }

    public function search_by_cpf(string $cpf) {
        $query = new WP_Query([
            'post_type'      => MERCADO_SOLIDARIO_FAMILY_POST,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => 'cpf',
                    'value'   => $cpf,
                    'compare' => '='
                ]
            ]
        ]);

        $families = $this->search_family($query);

        if ($families) {
            return Router::success_response($families);
        } else {
            return Router::error_response('error', 'Nenhuma familia encontrada');
        };

    }

    public function search_by_phone(string $phone) {
        $query = new WP_Query([
            'post_type'      => MERCADO_SOLIDARIO_FAMILY_POST,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => 'phone',
                    'value'   => $phone,
                    'compare' => '='
                ]
            ]
        ]);

        $families = $this->search_family($query);

        if ($families) {
            return Router::success_response($families);
        } else {
            return Router::error_response('error', 'Nenhuma familia encontrada');
        };
    }

    public function search_by_id(int $post_id){

        $query = new WP_Query([
            'post_type'      => MERCADO_SOLIDARIO_FAMILY_POST,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'p'              => $post_id
        ]);

        $families = $this->search_family($query);

        if ($families) {
            return Router::success_response($families);
        } else {
            return Router::error_response('error', 'Nenhuma familia encontrada');
        };
    }

    public function delete_family( WP_REST_Request $request ) {
        $family_id = $request['id'];

        if(!$family_id){
            return Router::error_response('erro', 'Faltou informar a família');
        };

        $response = wp_delete_post( $family_id, true );

        if ($response){
            return Router::success_response();
        } else {
            return Router::error_response('erro', 'Não foi possível deletar');
        }

    }

}