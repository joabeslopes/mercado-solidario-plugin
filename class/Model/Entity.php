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

class Entity extends Base\Model {

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
        string $name = ''
    ) {
        $this->set_id($id);
        $this->set_name($name);
    }

    public static function build_from_post(WP_Post $post): Entity {
        $id          = $post->ID;
        $name        = $post->post_title;

        $entity = new Entity($id, $name);

        return $entity;
    }

    public static function build_from_id(int $id): Entity {
        $post = get_post($id);

        return self::build_from_post($post);
    }

    public function save(): bool {
        if ( $this->name == '' || $this->cpf == '' || $this->phone == '' ){
            return false;
        };

        $post_id = wp_insert_post([
            'post_type'   => Controller\Entity::$post_type,
            'post_title'  => $this->name,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            return false;
        }

        $this->set_id($post_id);

        return true;
    }

    private function search_entity(WP_Query $query): array {

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
            'post_type'      => Controller\Entity::$post_type,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            's'              => $safe_name
        ]);

        return $this->search_entity($query);
    }

    public function get( WP_REST_Request $request ): WP_REST_Response {

        $query = new WP_Query([
            'post_type'      => Controller\Entity::$post_type,
            'posts_per_page' => -1, // All results
            'post_status'    => 'publish',
        ]);

        $all_entities = $this->search_entity($query);

        if (!empty($all_entities)) {
            $entities = array_column($all_entities, null, 'id');

            return $this->success_response($entities);
        } else {
            return $this->error_response('Nenhuma entidade encontrada');
        };
    }

    public function post( WP_REST_Request $request ): WP_REST_Response {
        $new_entity = $request['entity'];

        if(!$new_entity){
            return $this->error_response('Nenhuma entidade enviada');
        };

        $entity = new Entity(
            name: $new_entity['name']
        );

        $search = $this->search_by_name( $entity->name );
        if (!empty($search)){
            return $this->error_response('Entidade já cadastrada');
        };

        if ( $entity->save() ){
            return $this->success_response($entity);
        } else {
            return $this->error_response('Não foi possível salvar');
        };
    }

    public function delete( WP_REST_Request $request ): WP_REST_Response {
        $entity_id = $request['id'];

        if(!$entity_id){
            return $this->error_response('Nenhuma entidade informada');
        };

        $response = wp_delete_post( $entity_id, true );

        if ($response){
            return $this->success_response();
        } else {
            return $this->error_response('Não foi possível deletar');
        }

    }

}
