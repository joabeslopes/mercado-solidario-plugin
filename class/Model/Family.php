<?php

namespace Mercado_Solidario\Model;
use WP_Query;
use WP_Post;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Family {

    public int $id;
    public string $name;
    public string $cpf;
    public string $phone;
    public int $balance;
    public string $valid_until;
    public string $notes;

    // ID
    public function set_id($id): void {
        $this->id = (int) $id;
    }

    // Name
    public function set_name($name): void {
        $this->name = (string) $name;
    }

    // CPF
    public function set_cpf($cpf): void {
        $this->cpf = (string) $cpf;
    }

    // Phone
    public function set_phone($phone): void {
        $this->phone = (string) $phone;
    }

    // Balance
    public function set_balance($balance): void {
        $this->balance = (int) $balance;
    }

    // Valid Until
    public function set_valid_until($valid_until): void {
        $this->valid_until = (string) $valid_until;
    }

    // Notes
    public function set_notes($notes): void {
        $this->notes = (string) $notes;
    }

    public function __construct(
        int $id = 0,
        string $name = '',
        string $cpf = '',
        string $phone = '',
        int $balance = 0,
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

    public static function search_by_cpf(string $cpf): ?Family {
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

        if ($query->have_posts()) {
            $post = $query->posts[0];
            return self::build_from_post($post);
        }

        return null;
    }

    public static function search_by_phone(string $phone): ?Family {
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

        if ($query->have_posts()) {
            $post = $query->posts[0];
            return self::build_from_post($post);
        }

        return null;
    }

    public static function search_by_id(int $post_id): ?Family {
        $post = get_post($post_id);
    
        if (!$post || $post->post_type !== MERCADO_SOLIDARIO_FAMILY_POST) {
            return null;
        } else {
            return self::build_from_post($post);
        };
    }

    public static function search_all_valid_families(): array {
        $today = current_time('Y-m-d'); // Gets current date in site timezone
    
        $query = new WP_Query([
            'post_type'      => MERCADO_SOLIDARIO_FAMILY_POST,
            'posts_per_page' => -1, // All results
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => 'valid_until',
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'CHAR', // date stored as string
                ]
            ]
        ]);

        $families = [];

        if ($query->have_posts()) {
            foreach ($query->posts as $post) {
                $families[] = (array) self::build_from_post($post);
            }
        }
    
        return $families;
    }

    // Builder from WP_Post object
    private static function build_from_post(WP_Post $post): Family {
        $id          = $post->ID;
        $name        = $post->post_title;
        $cpf         = get_post_meta($id, 'cpf', true);
        $phone       = get_post_meta($id, 'phone', true);
        $balance     = (int) get_post_meta($id, 'balance', true);
        $valid_until = get_post_meta($id, 'valid_until', true);
        $notes       = get_post_meta($id, 'notes', true);

        return new Family($id, $name, $cpf, $phone, $balance, $valid_until, $notes);
    }

    public function save(): bool{
        if (empty($this->cpf)){
            return false;
        };

        // Prevent duplicates based on CPF
        if (self::search_by_cpf($this->cpf)) {
            return false;
        }

        // Insert new post
        $post_id = wp_insert_post([
            'post_type'   => MERCADO_SOLIDARIO_FAMILY_POST,
            'post_title'  => $this->name,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            return false;
        }

        // Save post meta
        update_post_meta($post_id, 'cpf', $this->cpf);
        update_post_meta($post_id, 'phone', $this->phone);
        update_post_meta($post_id, 'balance', $this->balance);
        update_post_meta($post_id, 'valid_until', $this->valid_until);
        update_post_meta($post_id, 'notes', $this->notes);

        $this->set_id($post_id);

        return true;
    }

};