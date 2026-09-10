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

    private function search_family(WP_Query | null $query): ?array {

        if ($query === null){
            return null;
        };

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

    public function build_query(array $search, $relation = 'AND'): ?WP_Query {
        if (empty($search)){
            return null;
        };

        $args = [];

        foreach ($search as $field_name => $value) {

            if ( $value === null || $value === '') continue;

            match ($field_name) {
                'id'   => $args['p'] = intval($value),

                'name' => $args['s'] = sanitize_text_field($value),

                default => $args['meta_query'][] = [
                    'key'     => $field_name,
                    'value'   => sanitize_text_field($value),
                    'compare' => '='
                ]
            };
        };

        if (!empty($args['meta_query'])){
            $args['meta_query']['relation'] = $relation;
        };

        if (empty($args)){
            return null;
        } else {
            $args['post_type'] = Controller\Families::$post_type;
            $args['posts_per_page'] = -1;
            $args['post_status'] = 'publish';

            return new WP_Query($args);
        };
    }

    private function build_search_array( WP_REST_Request $request ): array {
        $family = new Family();
        $attributes = $family->get_attributes();
        $search = [];

        foreach ($attributes as $field_name) {
            $value = $request->get_param($field_name);

            if ( $value === null || $value === '') continue;

            $search[$field_name] = $value;
        };

        return $search;
    }

    public function get( WP_REST_Request $request ): WP_REST_Response {

        $search = $this->build_search_array($request);

        if (empty($search)){
            return $this->error_response('Dados inválidos', 400);
        };

        $families = $this->search_family($this->build_query($search) );

        if ($families) {
            return $this->success_response($families);
        } else {
            return $this->error_response('Nenhuma familia encontrada', 404);
        };
    }

    public function post( WP_REST_Request $request ): WP_REST_Response {
        $new_family = $request['newFamily'];

        if(!$new_family){
            return $this->error_response('Dados inválidos', 400);
        };

        $family = new Family();

        $family->fill_from_array($new_family);

        // Pesquisa por dados pessoais
        $search = [
            'cpf' => $family->cpf,
            'phone' => $family->phone
        ];
        $existing_family = $this->search_family( $this->build_query($search, 'OR') );
        if ($existing_family){
            return $this->error_response('Família já cadastrada');
        };

        // Pesquisa por endereço completo
        $search = [
            'addr_cep' => $family->addr_cep,
            'addr_number' => $family->addr_number,
            'addr_compl' => $family->addr_compl,
        ];
        $existing_family = $this->search_family( $this->build_query($search) );
        if ($existing_family){
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
            return $this->error_response('Faltou informar a família', 400);
        };

        $response = wp_delete_post( $family_id, true );

        if ($response){
            return $this->success_response();
        } else {
            return $this->error_response('Não foi possível deletar');
        };

    }

    public function put( WP_REST_Request $request ): WP_REST_Response {
        $updated_family = $request['updatedFamily'];

        if(!$updated_family){
            return $this->error_response('Dados inválidos', 400);
        };

        $post = get_post($updated_family['id']);

        if($post == null){
            return $this->error_response('Dados inválidos', 400);
        };

        $family = Family::build_from_post($post);

        $family->fill_from_array($updated_family);

        if ( $family->save() ){
            return $this->success_response($family);
        } else {
            return $this->error_response('Não foi possível salvar');
        };
    }

}