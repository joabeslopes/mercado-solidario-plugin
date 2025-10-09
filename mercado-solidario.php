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

define('MERCADO_SOLIDARIO_URL', plugins_url( '/mercado-solidario'));
define('MERCADO_SOLIDARIO_DIR', plugin_dir_path( __FILE__));
define('MERCADO_SOLIDARIO_VERSION', '1.0');
define( 'MERCADO_SOLIDARIO_REST_NAMESPACE', 'mercado-solidario/v1' );

require_once('vendor/autoload.php' );

new Mercado_Solidario\Pages\Main_Page();

new Mercado_Solidario\REST\Router();

function remove_atum_roles(){
    $admin = get_role('administrator');
    if ($admin) {
        foreach ($admin->capabilities as $cap => $has_cap) {
            if (str_starts_with($cap, 'atum_')) {
                $admin->remove_cap($cap);
            }
        }
    }
};

function remove_atum_post_types() {
    global $wpdb;

    $post_types = $wpdb->get_col("
        SELECT DISTINCT post_type 
        FROM {$wpdb->posts}
        WHERE post_type LIKE 'atum_%'
    ");

    foreach ($post_types as $post_type) {
        $wpdb->query(
            $wpdb->prepare("DELETE pm FROM {$wpdb->postmeta} pm
                            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                            WHERE p.ID IS NULL OR p.post_type = %s", $post_type)
        );
        $wpdb->query(
            $wpdb->prepare("DELETE FROM {$wpdb->posts} WHERE post_type = %s", $post_type)
        );
    }
};

remove_atum_roles();
remove_atum_post_types();