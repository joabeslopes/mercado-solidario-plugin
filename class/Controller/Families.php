<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families extends Base{

    public static string $post_type = MERCADO_SOLIDARIO_POST_PREFIX.'family';

    public function __construct(){

        $this->model = new Model\Families();
        $this->base_route = 'families';

        add_action('init', [$this, 'register_family_post_type']);
        $this->register('get');
        $this->register('post');
        $this->register('delete');
    }

    public function register_family_post_type(){
        register_post_type(self::$post_type, [
            'public' => false
        ]);
    }

};