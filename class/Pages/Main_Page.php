<?php

namespace Mercado_Solidario\Pages;
use Mercado_Solidario\Security\CapabilitiesManager;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Main_Page {

    public static string $page_title = 'Mercado Solidario';
    public static string $menu_title = 'Mercado Solidario';
    public static string $capability;
    public static string $menu_slug = 'mercado-solidario';
    public static string $icon_url = MERCADO_SOLIDARIO_URL.'/frontend/assets/icon-mercado-solidario.svg';

    public function __construct() {
        self::$capability = CapabilitiesManager::$default;

        add_action('admin_menu',[$this,'create_page']);
        add_action('admin_menu',[$this,'hide_page'], 999);
    }

    public function create_page() {

        $menupage = add_menu_page(
            self::$page_title,
            self::$menu_title,
            self::$capability,
            self::$menu_slug,
            null,
            self::$icon_url
        );

        add_action( "admin_print_scripts", [$this,'print_settings'] );

        // Submenus
        new Families();
        new Checkout();
        new Checkin();

    }

    public function hide_page(){
        if (!current_user_can(self::$capability)) {
            remove_menu_page(self::$menu_slug);
        };
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