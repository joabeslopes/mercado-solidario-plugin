<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Controller;
use Mercado_Solidario\Base;
use WP_REST_Response;
use WP_REST_Request;
use WP_Query;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families extends Base\Model {

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

    public function get( WP_REST_Request $request ): WP_REST_Response {

        $cpf = $request->get_param('cpf');

        if ($cpf) {
            return $this->search_by_cpf($cpf);
        };

        $today = current_time('Y-m-d');

        $query = new WP_Query([
            'post_type'      => Controller\Families::$post_type,
            'posts_per_page' => -1, // All results
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => 'valid_until',
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'DATE'
                ]
            ]
        ]);

        $families = $this->search_family($query);

        if ($families) {
            return $this->success_response($families);
        } else {
            return $this->error_response('Nenhuma familia encontrada');
        };
    }

    public function post( WP_REST_Request $request ): WP_REST_Response {
        $new_family = $request['newFamily'];

        if(!$new_family){
            return $this->error_response('Faltou enviar a nova familia');
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
        if ($search->status == 200){
            return $this->error_response('Família já cadastrada');
        };

        $search = $this->search_by_phone( $family->phone );
        if ($search->status == 200){
            return $this->error_response('Família já cadastrada');
        };

        if ( $family->save() ){
            return $this->success_response($family);
        } else {
            return $this->error_response('Não foi possível salvar');
        };
    }

    public function delete( WP_REST_Request $request ): WP_REST_Response {
        $family_id = $request['id'];

        if(!$family_id){
            return $this->error_response('Faltou informar a família');
        };

        $response = wp_delete_post( $family_id, true );

        if ($response){
            return $this->success_response();
        } else {
            return $this->error_response('Não foi possível deletar');
        }

    }

    public function search_by_cpf(string $cpf) {
        $query = new WP_Query([
            'post_type'      => Controller\Families::$post_type,
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
            return $this->success_response($families);
        } else {
            return $this->error_response('Nenhuma familia encontrada');
        };

    }

    public function search_by_phone(string $phone) {
        $query = new WP_Query([
            'post_type'      => Controller\Families::$post_type,
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
            return $this->success_response($families);
        } else {
            return $this->error_response('Nenhuma familia encontrada');
        };
    }

    public function search_by_id(int $post_id){

        $query = new WP_Query([
            'post_type'      => Controller\Families::$post_type,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'p'              => $post_id
        ]);

        $families = $this->search_family($query);

        if ($families) {
            return $this->success_response($families);
        } else {
            return $this->error_response('Nenhuma familia encontrada');
        };
    }

}