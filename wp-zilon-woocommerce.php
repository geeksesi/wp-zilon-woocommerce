<?php
/*
Plugin name: wp-zilon-woocommerce
Plugin URI: https://gitlab.com/zilon/zilon-woocommerce-plugin
Description: Zilon.io for woocommerce 
Version: 1.0.0
Author: mohammad javad ghasemy 
Author URI: 
Text Domain: -e('wp zilon woocommerce','wp-zilon-woocommerce')
 */
defined('ABSPATH') || exit('No Direct Access.');
define('WP_ZILON_WOOCOMMERCE_DIR', plugin_dir_path(__FILE__));
define('WP_ZILON_WOOCOMMERCE_URL', plugin_dir_url(__FILE__));
define('WP_ZILON_WOOCOMMERCE_IMG_URL', trailingslashit(WP_ZILON_WOOCOMMERCE_URL.'assets/img'));
define('WP_ZILON_WOOCOMMERCE_INC_DIR', trailingslashit(WP_ZILON_WOOCOMMERCE_DIR.'include'));
define('WP_ZILON_WOOCOMMERCE_VERSION', "1.0.0");


include WP_ZILON_WOOCOMMERCE_INC_DIR.'class/controller/WP_ZILON_WOOCOMMERCE_Controller.php';
$zilon = new WP_ZILON_WOOCOMMERCE_Controller();

add_action('plugins_loaded', 'WP_ZILON_WOOCOMMERCE_textdomain');
function WP_ZILON_WOOCOMMERCE_textdomain() {
	load_plugin_textdomain( 'wp-zilon-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

