<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Controller;
use Mercado_Solidario\Base;
use WP_REST_Response;
use WP_Post;
use WP_REST_Request;
use WP_Query;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Supplier extends Base\Model {

    public int $id;
    public string $name;

    public function set_id($id): void {
        $this->id = (int) sanitize_text_field($id);
    }

    public function set_name($name): void {
        $this->name = sanitize_text_field($name);
    }

    public function __construct(
        int $id = 0,
        string $name = '',
    ) {
        $this->set_id($id);
        $this->set_name($name);
    }

    public static function build_from_post(WP_Post $post): Supplier {
        $id          = $post->ID;
        $name        = $post->post_title;

        return new Supplier($id, $name);
    }

    public static function build_from_id(int $id): Supplier {
        $post = get_post($id);

        return self::build_from_post($post);
    }

    public function save(): bool {
        if ( $this->name == '' ){
            return false;
        };

        $post_id = wp_insert_post([
            'post_type'   => Controller\Supplier::$post_type,
            'post_title'  => $this->name,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            return false;
        }

        $this->set_id($post_id);

        return true;
    }

    private function search_supplier(WP_Query $query): array {

        $result = [];

        if ($query->have_posts()) {
            $posts = $query->get_posts();
            foreach ($posts as $post) {
                $result[] = (array) self::build_from_post($post);
            };
        };
        return $result;
    }

    public function search_by_name(string $name): array {
        $safe_name = sanitize_text_field($name);

        $query = new WP_Query([
            'post_type'      => Controller\Supplier::$post_type,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            's'              => $safe_name
        ]);

        return $this->search_supplier($query);
    }

    public function get( WP_REST_Request $request ): WP_REST_Response {

        $query = new WP_Query([
            'post_type'      => Controller\Supplier::$post_type,
            'posts_per_page' => -1, // All results
            'post_status'    => 'publish',
        ]);

        $all_suppliers = $this->search_supplier($query);

        if (!empty($all_suppliers)) {
            $suppliers = array_column($all_suppliers, null, 'id');

            return $this->success_response($suppliers);
        } else {
            return $this->error_response('Nenhum fornecedor encontrado');
        };
    }

    public function post( WP_REST_Request $request ): WP_REST_Response {
        $new_supplier = $request['supplier'];

        if(!$new_supplier){
            return $this->error_response('Nenhum fornecedor enviado');
        };

        $supplier = new Supplier(
            name: $new_supplier['name'],
        );

        $search = $this->search_by_name( $supplier->name );
        if (!empty($search)){
            return $this->error_response('Fornecedor já cadastrado');
        };

        if ( $supplier->save() ){
            return $this->success_response($supplier);
        } else {
            return $this->error_response('Não foi possível salvar');
        };
    }

    public function delete( WP_REST_Request $request ): WP_REST_Response {
        $supplier_id = $request['id'];

        if(!$supplier_id){
            return $this->error_response('Nenhum fornecedor informado');
        };

        $response = wp_delete_post( $supplier_id, true );

        if ($response){
            return $this->success_response();
        } else {
            return $this->error_response('Não foi possível deletar');
        }

    }

};