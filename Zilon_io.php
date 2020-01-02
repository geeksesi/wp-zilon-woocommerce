<?php
/*
Plugin name: Zilon_io
Plugin URI: 
Description: Zilon.io for wordpress 
Version: v1.0
Author: Zilon 
Author URI: 
Text Domain: -e('Zilon.io','Zilon_io')
 */
defined('ABSPATH') || exit('No Direct Access.');
define('ZILONIO_DIR', plugin_dir_path(__FILE__));
define('ZILONIO_URL', plugin_dir_url(__FILE__));
define('ZILONIO_IMG_URL', trailingslashit(ZILONIO_URL.'assets/img'));
define('ZILONIO_INC_DIR', trailingslashit(ZILONIO_DIR.'include'));
define('ZILONIO_VERSION', "0.1.0");


include ZILONIO_INC_DIR.'class/controller/Controller.php';
$zilon = new Controller();

add_action('plugins_loaded', 'ZILONIO_textdomain');
function ZILONIO_textdomain() {
	load_plugin_textdomain( 'Zilon_io', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

