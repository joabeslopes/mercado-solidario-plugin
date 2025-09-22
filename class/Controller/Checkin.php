<?php

namespace Mercado_Solidario\Controller;
use Mercado_Solidario\Model;
use Mercado_Solidario\Pages;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkin extends Base{

    public function __construct(){

        $this->model = new Model\Checkin();
        $this->base_route = 'checkin';
        self::$post_type = MERCADO_SOLIDARIO_POST_PREFIX.'checkin';

        add_action('init', [$this, 'register_checkin_post_type']);
        $this->register('post');
    }

    public function register_checkin_post_type() {
        register_post_type(self::$post_type, [
            'public' => false,
            'show_ui' => true,
            'supports' => ['title'],
            'show_in_menu' => Pages\Main_Page::$menu_slug,
            'capabilities' => [
                'create_posts' => 'do_not_allow'
            ],
            'map_meta_cap' => true,
            'labels' => [
                'name' => 'Histórico de entradas'
            ]
        ]);

        new Pages\Checkin_Post();
    }

};