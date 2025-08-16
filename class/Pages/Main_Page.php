<?php

namespace Mercado_Solidario\Pages;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Main_Page {

    private string $page_title = 'Mercado Solidario';
    private string $menu_title = 'Mercado Solidario';
    private string $capability = 'manage_woocommerce';
    private string $menu_slug = 'mercado-solidario';
    private string $icon_url = MERCADO_SOLIDARIO_URL.'/frontend/assets/icon-mercado-solidario.svg';

    public function __construct() {
        add_action('admin_menu',[$this,'create_page']);
    }

    public function show_page(){
        $caixa_page = file_get_contents( MERCADO_SOLIDARIO_DIR.'/frontend/dist/index.html');
        echo $caixa_page;
    }

    public function create_page() {

        $menupage = add_menu_page(
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            [$this,'show_page'],
            $this->icon_url
        );

        add_action( "admin_print_scripts-$menupage", [$this,'print_settings'] );

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