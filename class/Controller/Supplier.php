<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;
use Mercado_Solidario\Base;
use Mercado_Solidario\Pages;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Supplier extends Base\Controller{

    public static string $post_type = MERCADO_SOLIDARIO_POST_PREFIX.'supplier';

    public function __construct(){

        $this->model = new Model\Supplier();
        $this->base_route = 'supplier';

        add_action('init', [$this, 'load_post_type']);
        $this->register('get');
        $this->register('post');
        $this->register('delete');
    }

    public function load_post_type(){
        register_post_type(self::$post_type, [
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'custom-fields'],
            'show_in_menu' => Pages\Main_Page::$menu_slug,
            'labels' => [
                'name' => 'Fornecedores'
            ]
        ]);
    }

};