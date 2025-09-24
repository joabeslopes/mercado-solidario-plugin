<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;
use Mercado_Solidario\Base;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Families extends Base\Controller{

    public static string $post_type = MERCADO_SOLIDARIO_POST_PREFIX.'family';

    public function __construct(){

        $this->model = new Model\Families();
        $this->base_route = 'families';

        add_action('init', [$this, 'load_post_type']);
        $this->register('get');
        $this->register('post');
        $this->register('delete');
    }

    public function load_post_type(){
        register_post_type(self::$post_type, [
            'public' => false
        ]);
    }

};