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

class EntityPerson extends Base\Model {

    public int $id;
    public int $person_id;
    public int $entity_id;
    public string $status;
    public string $notes;

    private array $allowed_status = ['planned', 'active', 'completed', 'inactive'];

    public function set_id($id): void {
        $this->id = (int) sanitize_text_field($id);
    }

    public function set_person_id($person_id): void {
        $this->person_id = (int) sanitize_text_field($person_id);
    }

    public function set_entity_id($entity_id): void {
        $this->entity_id = (int) sanitize_text_field($entity_id);
    }

    public function set_status($status): void {
        $sanit_status = sanitize_text_field($status);
        if (!$sanit_status){
            $sanit_status = 'planned';
        };

        if (in_array($sanit_status, $this->allowed_status)) {
            $this->status = $sanit_status;
        };
    }

    public function set_notes($notes): void {
        $this->notes = sanitize_text_field($notes);
    }

    public function save(): bool {
        $post_id = wp_insert_post([
            'post_type' => Controller\EntityPerson::$post_type,
            'post_title' => "{$this->person_id}_{$this->entity_id}",
            'post_status' => 'publish',
        ]);

        if ($post_id) {
            $this->id = $post_id;

            $attributes = $this->get_attributes();

            foreach ($attributes as $key){
                if ($key == 'id'){
                    continue;
                };
                update_post_meta($post_id, $key, $this->$key);
            }

            return true;
        }

        return false;
    }

    public static function build_from_post(WP_Post $post): EntityPerson {
        $entity_person = new EntityPerson();
        $id = $post->ID;

        $entity_person->set_id($id);

        $attributes = $entity_person->get_attributes();

        foreach ($attributes as $key){
            if ($key == 'id'){
                continue;
            };

            $method_name = 'set_' . $key;
            if (method_exists($entity_person, $method_name)) {
                $entity_person->$method_name(get_post_meta($id, $key, true));
            };
        };

        return $entity_person;
    }

    public function get_by_person_and_entity(int $person_id, int $entity_id): ?self {
        $query = new WP_Query([
            'post_type' => Controller\EntityPerson::$post_type,
            'meta_query' => [
                [
                    'key' => 'person_id',
                    'value' => $person_id,
                    'compare' => '='
                ],
                [
                    'key' => 'entity_id',
                    'value' => $entity_id,
                    'compare' => '='
                ]
            ]
        ]);

        if ($query->have_posts()) {
            $post = $query->get_posts()[0];
            $entity_person = self::build_from_post($post);

            return $entity_person;
        }

        return null;
    }

    public function get_by_person(int $person_id): array {
        $query = new WP_Query([
            'post_type' => Controller\EntityPerson::$post_type,
            'meta_query' => [
                [
                    'key' => 'person_id',
                    'value' => $person_id,
                    'compare' => '='
                ]
            ]
        ]);

        $result = [];
        if ($query->have_posts()) {
            foreach ($query->posts as $post) {
                $entity_person = self::build_from_post($post);
                $result[] = $entity_person;
            }
        }
        
        return $result;
    }

    public function update_status(int $person_id, int $entity_id, string $status): bool {
        $entity_person = $this->get_by_person_and_entity($person_id, $entity_id);
        
        if ($entity_person) {
            update_post_meta($entity_person->id, 'status', $status);
            return true;
        }

        return false;
    }

    public function update_notes(int $person_id, int $entity_id, string $notes): bool {
        $entity_person = $this->get_by_person_and_entity($person_id, $entity_id);
        
        if ($entity_person) {
            update_post_meta($entity_person->id, 'notes', $notes);
            return true;
        }
        
        return false;
    }

    public function get(WP_REST_Request $request): WP_REST_Response {
        $person_id = $request->get_param('person_id');
        $entity_id = $request->get_param('entity_id');
        $persons = [];

        if ($person_id && $entity_id) {
            $result = $this->get_by_person_and_entity($person_id, $entity_id);
            $persons = $result ? (array) $result : [];
        } elseif ($person_id) {
            $persons = $this->get_by_person($person_id);
        } else {
            return $this->error_response('Parâmetros inválidos', 400);
        };

        if ($persons){
            return $this->success_response($persons);
        } else {
            return $this->error_response('Relação não encontrada', 404);
        };
    }

    public function post(WP_REST_Request $request): WP_REST_Response {
        $data = $request->get_json_params();
        $person_id = $data['person_id'] ?? $request->get_param('person_id');
        $entity_id = $data['entity_id'] ?? $request->get_param('entity_id');
        
        if (!$person_id || !$entity_id) {
            return $this->error_response('Dados inválidos: person_id e entity_id são obrigatórios', 400);
        }

        if ($this->get_by_person_and_entity($person_id, $entity_id)){
            return $this->error_response('Relação já existe', 500);
        }

        $entity_person = new EntityPerson();

        $entity_person->set_person_id($person_id);
        $entity_person->set_entity_id($entity_id);
        $entity_person->set_status($data['status'] ?? 'planned');
        $entity_person->set_notes($data['notes'] ?? '');

        if ($entity_person->save()) {
            return $this->success_response($entity_person);
        } else {
            return $this->error_response('Não foi possível salvar a relação', 500);
        }
    }

    public function put(WP_REST_Request $request): WP_REST_Response {
        $data = $request->get_json_params();
        $person_id = $request->get_param('person_id') ?? $data['person_id'] ?? null;
        $entity_id = $request->get_param('entity_id') ?? $data['entity_id'] ?? null;
        
        if (!$person_id || !$entity_id) {
            return $this->error_response('Dados inválidos: person_id e entity_id são obrigatórios', 400);
        }

        $entity_person = new EntityPerson();

        if (isset($data['status'])) {
            if ($entity_person->update_status($person_id, $entity_id, $data['status'])) {
                // Se também houver notes, atualiza logo em seguida
                if (isset($data['notes'])) {
                    $entity_person->update_notes($person_id, $entity_id, $data['notes']);
                }
                return $this->success_response('Relação atualizada com sucesso');
            } else {
                return $this->error_response('Não foi possível atualizar o status', 500);
            }
        }
        
        if (isset($data['notes'])) {
            if ($entity_person->update_notes($person_id, $entity_id, $data['notes'])) {
                return $this->success_response('Observações atualizadas com sucesso');
            } else {
                return $this->error_response('Não foi possível atualizar as observações', 500);
            }
        }
        
        return $this->error_response('Nenhum dado válido fornecido para atualização', 400);
    }

    public function delete(WP_REST_Request $request): WP_REST_Response {
        $person_id = $request->get_param('person_id');
        $entity_id = $request->get_param('entity_id');

        if (!$person_id || !$entity_id) {
            return $this->error_response('Dados inválidos: person_id e entity_id são obrigatórios', 400);
        }

        $entity_person = $this->get_by_person_and_entity($person_id, $entity_id);

        if ($entity_person) {
            wp_delete_post($entity_person->id, true);
            return $this->success_response('Relação excluída com sucesso');
        } else {
            return $this->error_response('Não foi possível excluir a relação', 500);
        };

    }

}
