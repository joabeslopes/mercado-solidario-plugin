<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Controller;
use Mercado_Solidario\Base;
use WP_REST_Response;
use WP_REST_Request;
use WP_Query;
use WP_Post;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class FamilyDependant extends Base\Model {

    public int $id;
    public string $name;
    public int $family_id;
    public string $birth_date;
    public string $relation;

    public function set_id($id): void {
        $this->id = (int) $id;
    }

    public function set_name($name): void {
        $this->name = sanitize_text_field($name);
    }

    public function set_family_id($family_id): void {
        $this->family_id = (int) $family_id;
    }

    public function set_birth_date($birth_date): void {
        $this->birth_date = sanitize_text_field($birth_date);
    }

    public function set_relation($relation): void {
        $this->relation = sanitize_text_field($relation);
    }

    public function save(): bool {
        $post_data = [
            'post_type' => Controller\FamilyDependant::$post_type,
            'post_title' => $this->name,
            'post_status' => 'publish',
        ];

        if (isset($this->id) && $this->id > 0) {
            $post_data['ID'] = $this->id;
            $post_id = wp_update_post($post_data);
        } else {
            $post_id = wp_insert_post($post_data);
        }

        if ($post_id && !is_wp_error($post_id)) {
            $this->id = $post_id;

            $attributes = $this->get_attributes();

            foreach ($attributes as $key){
                if ($key == 'id' || $key == 'name'){
                    continue;
                };

                if (isset($this->$key)){
                    update_post_meta($this->id, $key, $this->$key);
                };
            };

            return true;
        }

        return false;
    }

    public static function build_from_post(WP_Post $post): FamilyDependant {
        $dependant = new FamilyDependant();
        $id = $post->ID;

        $dependant->set_id($id);
        $dependant->set_name($post->post_title);

        $attributes = $dependant->get_attributes();

        foreach ($attributes as $key){
            if ($key == 'id' || $key == 'name'){
                continue;
            };

            $method_name = 'set_' . $key;
            if (method_exists($dependant, $method_name)) {
                $dependant->$method_name(get_post_meta($id, $key, true));
            };
        };

        return $dependant;
    }

    public function get_by_family(int $family_id): array {
        $query = new WP_Query([
            'post_type' => Controller\FamilyDependant::$post_type,
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'family_id',
                    'value' => $family_id,
                    'compare' => '='
                ]
            ]
        ]);

        $result = [];
        if ($query->have_posts()) {
            foreach ($query->posts as $post) {
                $result[] = self::build_from_post($post);
            }
        }
        
        return $result;
    }

    public function get_by_id(int $id): ?self {
        $post = get_post($id);
        if ($post && $post->post_type === Controller\FamilyDependant::$post_type) {
            return self::build_from_post($post);
        }
        return null;
    }

    public function get(WP_REST_Request $request): WP_REST_Response {
        $family_id = $request->get_param('family_id');
        $dependant_id = $request->get_param('dependant_id');

        if ($dependant_id) {
            $dependant = $this->get_by_id($dependant_id);
            if ($dependant) {
                if ($family_id && $dependant->family_id != $family_id) {
                    return $this->error_response('Dependente não pertence a esta família', 403);
                }
                return $this->success_response($dependant);
            }
            return $this->error_response('Dependente não encontrado', 404);
        }

        if ($family_id) {
            $dependants = $this->get_by_family($family_id);
            return $this->success_response($dependants);
        }

        return $this->error_response('family_id é obrigatório', 400);
    }

    public function post(WP_REST_Request $request): WP_REST_Response {
        $family_id = $request->get_param('family_id');
        $data = $request->get_params();

        if (!$family_id) {
            return $this->error_response('family_id é obrigatório', 400);
        }

        if (!isset($data['name']) || !isset($data['birth_date'])) {
            return $this->error_response('Nome e nascimento são obrigatórios', 400);
        }

        $dependant = new FamilyDependant();
        $dependant->set_family_id($family_id);
        $dependant->set_name($data['name']);
        $dependant->set_birth_date($data['birth_date']);
        $dependant->set_relation($data['relation']);

        if ($dependant->save()) {
            return $this->success_response($dependant);
        } else {
            return $this->error_response('Não foi possível salvar o dependente', 500);
        }
    }

    public function put(WP_REST_Request $request): WP_REST_Response {
        $family_id = $request->get_param('family_id');
        $dependant_id = $request->get_param('dependant_id');
        $data = $request->get_params();

        if (!$dependant_id) {
            return $this->error_response('dependant_id é obrigatório', 400);
        }

        $dependant = $this->get_by_id($dependant_id);
        if (!$dependant) {
            return $this->error_response('Dependente não encontrado', 404);
        }

        if ($family_id && $dependant->family_id != $family_id) {
            return $this->error_response('Dependente não pertence a esta família', 403);
        }

        if (isset($data['name'])) {
            $dependant->set_name($data['name']);
        }
        if (isset($data['birth_date'])) {
            $dependant->set_birth_date($data['birth_date']);
        }
        if (isset($data['relation'])) {
            $dependant->set_relation($data['relation']);
        }

        if ($dependant->save()) {
            return $this->success_response($dependant);
        } else {
            return $this->error_response('Não foi possível atualizar o dependente', 500);
        }
    }

    public function delete(WP_REST_Request $request): WP_REST_Response {
        $family_id = $request->get_param('family_id');
        $dependant_id = $request->get_param('dependant_id');

        if (!$dependant_id) {
            return $this->error_response('dependant_id é obrigatório', 400);
        }

        $dependant = $this->get_by_id($dependant_id);
        if (!$dependant) {
            return $this->error_response('Dependente não encontrado', 404);
        }

        if ($family_id && $dependant->family_id != $family_id) {
            return $this->error_response('Dependente não pertence a esta família', 403);
        }

        if (wp_delete_post($dependant_id, true)) {
            return $this->success_response('Dependente excluído com sucesso');
        } else {
            return $this->error_response('Não foi possível excluir o dependente', 500);
        }
    }

}
