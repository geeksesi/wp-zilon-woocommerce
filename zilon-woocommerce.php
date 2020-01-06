<?php
/*
Plugin name: zilon-woocommerce
Plugin URI: https://gitlab.com/zilon/zilon-woocommerce-plugin
Description: Zilon.io for woocommerce 
Version: 1.0
Author: Zilon 
Author URI: https://zilon.io
Text Domain: -e('Zilon woocommerce','zilon-woocommerce')
 */
defined('ABSPATH') || exit('No Direct Access.');
define('ZILON_WOOCOMMERCE_DIR', plugin_dir_path(__FILE__));
define('ZILON_WOOCOMMERCE_URL', plugin_dir_url(__FILE__));
define('ZILON_WOOCOMMERCE_IMG_URL', trailingslashit(ZILON_WOOCOMMERCE_URL.'assets/img'));
define('ZILON_WOOCOMMERCE_INC_DIR', trailingslashit(ZILON_WOOCOMMERCE_DIR.'include'));
define('ZILON_WOOCOMMERCE_VERSION', "1.0.0");


include ZILON_WOOCOMMERCE_INC_DIR.'class/controller/ZILON_WOOCOMMERCE_Controller.php';
$zilon = new ZILON_WOOCOMMERCE_Controller();

add_action('plugins_loaded', 'ZILON_WOOCOMMERCE_textdomain');
function ZILON_WOOCOMMERCE_textdomain() {
	load_plugin_textdomain( 'zilon-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

