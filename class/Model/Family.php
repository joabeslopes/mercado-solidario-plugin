<?php

namespace Mercado_Solidario\Model;
use Mercado_Solidario\Controller;
use Mercado_Solidario\Base;
use WP_Post;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Family extends Base\Model {

    public int $id;
    public int $year;
    public int $period;
    public string $name;
    public string $cpf;
    public string $phone;
    public string $birth_date;
    public string $notes = '';
    public string $addr_cep;
    public int $addr_number;
    public string $addr_compl = '';

    public function set_id($id): void {
        $this->id = (int) sanitize_text_field($id);
    }

    public function set_year($year): void {
        $this->year = (int) sanitize_text_field($year);
    }

    public function set_period($period): void {
        $this->period = (int) sanitize_text_field($period);
    }

    public function set_name($name): void {
        $this->name = sanitize_text_field($name);
    }

    public function set_cpf($cpf): void {
        $sanit_cpf = strtoupper( sanitize_text_field($cpf) );
        $sanit_cpf = preg_replace('/[^A-Z0-9]/','', $sanit_cpf);
        $this->cpf = $sanit_cpf;
    }

    public function set_phone($phone): void {
        $this->phone = preg_replace('/[^0-9]/','', sanitize_text_field($phone));
    }

    public function set_birth_date($birth_date): void {
        $this->birth_date = sanitize_text_field($birth_date);
    }

    public function set_notes($notes): void {
        $this->notes = sanitize_text_field($notes);
    }

    public function set_addr_cep($cep): void {
        $this->addr_cep = preg_replace('/[^0-9]/','', sanitize_text_field($cep));
    }

    public function set_addr_number($addr_number): void {
        $this->addr_number = (int) sanitize_text_field($addr_number);
    }

    public function set_addr_compl($addr_compl): void {
        $this->addr_compl = sanitize_text_field($addr_compl);
    }

    public static function build_from_post(WP_Post $post): Family {
        $family = new Family();
        $id = $post->ID;

        $family->set_id($id);
        $family->set_name($post->post_title);

        $attributes = $family->get_attributes();

        foreach ($attributes as $key){
            if ($key == 'id' || $key == 'name'){
                continue;
            };

            $method_name = 'set_' . $key;
            if (method_exists($family, $method_name)) {
                $family->$method_name(get_post_meta($id, $key, true));
            };
        };

        return $family;
    }

    public function save(): bool {
        if ( empty($this->year) || empty($this->period) ||  $this->name == '' || $this->cpf == '' || $this->phone == '' || $this->addr_cep == '' || empty($this->addr_number) ){
            return false;
        };

        if (!isset($this->id)) {
            $post_id = wp_insert_post([
                'post_type'   => Controller\Families::$post_type,
                'post_title'  => $this->name,
                'post_status' => 'publish',
            ]);
    
            if (is_wp_error($post_id)) {
                return false;
            } else {
                $this->set_id($post_id);
            };
        };

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

    public function fill_from_array(array $data): void {
        foreach ($data as $key => $value) {
            $method_name = 'set_' . $key;

            if (method_exists($this, $method_name)) {
                $this->$method_name($value);
            };
        };
    }

};