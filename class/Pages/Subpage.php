<?php

namespace Mercado_Solidario\Pages;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Subpage {

    public string $page_title = '';
    public string $menu_title = '';
    public string $capability = MERCADO_SOLIDARIO_CAPABILITY;
    public string $menu_slug = '';

    public function __construct(int $position) {
        $this->create_subpage($position);
    }

    public function getShortName(): string {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function show_page(){
        $page_content = file_get_contents( MERCADO_SOLIDARIO_DIR.'/frontend/dist/src/pages/'.$this->getShortName().'/index.html');
        echo $page_content;
    }

    public function create_subpage(int $position) {

        add_submenu_page(
            Main_Page::$menu_slug,
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            [$this,'show_page'],
            $position
        );
    }

}