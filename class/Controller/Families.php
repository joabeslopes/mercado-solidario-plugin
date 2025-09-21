<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families extends Base{

    public function __construct(){

        $this->model = new Model\Families();
        $this->base_route = 'families';

        add_action('init', [$this, 'register_family_post_type']);
        $this->register('get');
        $this->register('post');
        $this->register('delete');
    }

    public function register_family_post_type(){
        register_post_type(MERCADO_SOLIDARIO_FAMILY_POST, [
            'public' => false
        ]);
    }

};