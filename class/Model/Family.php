<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Controller;
use WP_Post;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Family {

    public int $id;
    public string $name;
    public string $cpf;
    public string $phone;
    public float $balance;
    public string $valid_until;
    public string $notes;

    public function set_id($id): void {
        $this->id = (int) sanitize_text_field($id);
    }

    public function set_name($name): void {
        $this->name = sanitize_text_field($name);
    }

    public function set_cpf($cpf): void {
        $this->cpf = sanitize_text_field($cpf);
    }

    public function set_phone($phone): void {
        $this->phone = sanitize_text_field($phone);
    }

    public function set_balance($balance): void {
        $this->balance = (float) sanitize_text_field($balance);
    }

    public function set_valid_until($valid_until): void {
        $this->valid_until = sanitize_text_field($valid_until);
    }

    public function set_notes($notes): void {
        $this->notes = sanitize_text_field($notes);
    }

    public function __construct(
        int $id = 0,
        string $name = '',
        string $cpf = '',
        string $phone = '',
        float $balance = 0,
        string $valid_until = '',
        string $notes = ''
    ) {
        $this->set_id($id);
        $this->set_name($name);
        $this->set_cpf($cpf);
        $this->set_phone($phone);
        $this->set_balance($balance);
        $this->set_valid_until($valid_until);
        $this->set_notes($notes);
    }

    public static function build_from_post(WP_Post $post): Family {
        $id          = $post->ID;
        $name        = $post->post_title;
        $cpf         = get_post_meta($id, 'cpf', true);
        $phone       = get_post_meta($id, 'phone', true);
        $balance     = (int) get_post_meta($id, 'balance', true);
        $valid_until = get_post_meta($id, 'valid_until', true);
        $notes       = get_post_meta($id, 'notes', true);

        return new Family($id, $name, $cpf, $phone, $balance, $valid_until, $notes);
    }

    public function save(): bool {
        if ( $this->name == '' || $this->cpf == '' || $this->phone == '' || $this->valid_until == '' ){
            return false;
        };

        $post_id = wp_insert_post([
            'post_type'   => Controller\Families::$post_type,
            'post_title'  => $this->name,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            return false;
        }

        update_post_meta($post_id, 'cpf', $this->cpf);
        update_post_meta($post_id, 'phone', $this->phone);
        update_post_meta($post_id, 'balance', $this->balance);
        update_post_meta($post_id, 'valid_until', $this->valid_until);
        update_post_meta($post_id, 'notes', $this->notes);

        $this->set_id($post_id);

        return true;
    }

};