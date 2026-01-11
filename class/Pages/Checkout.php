<?php

namespace Mercado_Solidario\Pages;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class Checkout extends Subpage {

    public string $page_title = 'Caixa';
    public string $menu_title = 'Caixa';
    public string $capability = MERCADO_SOLIDARIO_CAPABILITY;
    public string $menu_slug = 'mercado-solidario-checkout';

}