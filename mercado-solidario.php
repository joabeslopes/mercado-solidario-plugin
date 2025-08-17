<?php
/*
Plugin Name: Mercado solidario
Plugin URI: https://mercadosolidariorp.com.br
Text Domain: mercado-solidario
Requires Plugins: woocommerce
Description: Mercado solidario
Author: Joabe Lopes
Version: 1.0
Author URI: https://sitejoabe.mooo.com
Requires at least:    6.0
Tested up to:         6.7
Requires PHP:         8.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// don't call the file directly
defined( 'ABSPATH' ) || die;

require_once('vendor/autoload.php' );

define('MERCADO_SOLIDARIO_URL', plugins_url( '/mercado-solidario'));
define('MERCADO_SOLIDARIO_DIR', plugin_dir_path( __FILE__));
define('MERCADO_SOLIDARIO_VERSION', '1.0');
define( 'MERCADO_SOLIDARIO_REST_NAMESPACE', 'mercado-solidario/v1' );
define('MERCADO_SOLIDARIO_FAMILY_POST', 'ms_family');
define('MERCADO_SOLIDARIO_CAPABILITY', 'manage_woocommerce');

new Mercado_Solidario\Pages\Main_Page();

new Mercado_Solidario\REST\Router();