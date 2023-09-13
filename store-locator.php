<?php
/*
  Plugin Name: WP Multi Store Locator Free  
  Plugin URI: https://wpexperts.io/products/wp-multi-store-locator/
  Description: This plugin provides a number of options for admin in backend to manage their stores and sales manager for respective franchise. WP Store Locator have awesome user interface and displays results with google map in front end. Its a complete package with lots of features like search store, nearby you stores functionality and much more.
  Version: 2.4.1
  Author: WpExpertsio
  Author URI: https://wpexperts.io/
  Text Domain: store_locator
  License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
define('STORE_LOCATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('STORE_LOCATOR_PLUGIN_PATH', plugin_dir_path(__FILE__));

include STORE_LOCATOR_PLUGIN_PATH . 'inc/store_locator_widget.php';
include STORE_LOCATOR_PLUGIN_PATH . 'inc/class-manage-stores.php';
include STORE_LOCATOR_PLUGIN_PATH . 'inc/class-front-end-controller.php';
include STORE_LOCATOR_PLUGIN_PATH . 'inc/class-stores-backend.php';
include STORE_LOCATOR_PLUGIN_PATH . 'inc/class-multi-maps-backend.php';
include STORE_LOCATOR_PLUGIN_PATH . 'inc/class-stores-frontend.php';
include STORE_LOCATOR_PLUGIN_PATH . 'inc/class-multi-maps-frontend.php';
//create tables
register_activation_hook(__FILE__, 'store_locator_plugin_activation');
function store_locator_plugin_activation() {
    include STORE_LOCATOR_PLUGIN_PATH . 'inc/install.php';
}
// Multi Languages code here //
add_action('init','wpmsl_add_translation');
function wpmsl_add_translation() {
     load_plugin_textdomain('store_locator', FALSE,  basename( dirname( __FILE__ ) ) . '/languages/');
}

add_filter( 'template_include', 'store_locator_single_id_template', 99 );
function store_locator_single_id_template( $template ) {
    $post_id = get_the_ID();
    $post = get_post($post_id);

    if ( is_single() &&  $post->post_type == "store_locator" ) {
        $template = STORE_LOCATOR_PLUGIN_PATH . 'templates/single-store_locator.php';
    }

    return $template;
}
