<?php

namespace Mercado_Solidario\Pages;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Main_Page {

    public static string $page_title = 'Mercado Solidario';
    public static string $menu_title = 'Mercado Solidario';
    public static string $capability = MERCADO_SOLIDARIO_CAPABILITY;
    public static string $menu_slug = 'mercado-solidario';
    public static string $icon_url = MERCADO_SOLIDARIO_URL.'/frontend/assets/icon-mercado-solidario.svg';

    public function __construct() {
        add_action('admin_menu',[$this,'create_page']);
    }

    public function create_page() {

        $menupage = add_menu_page(
            self::$page_title,
            self::$menu_title,
            self::$capability,
            self::$menu_slug,
            [$this,'show_page'],
            self::$icon_url
        );

        add_action( "admin_print_scripts", [$this,'print_settings'] );

        // Submenus
        new Checkin(0);
        new Checkout(1);
        new Families(2);

    }

    public function print_settings(){

        $settings = [
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'namespace' => get_rest_url( path: MERCADO_SOLIDARIO_REST_NAMESPACE )
        ];

        $settings_json = json_encode($settings);

        echo "<script> var mercadoSolidarioSettings = $settings_json </script>";
    }

}