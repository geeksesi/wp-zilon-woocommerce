<?php
/*
Plugin name: Zilon_io
Plugin URI: 
Description: Zilon.io for wordpress 
Version: v0.1.0
Author: Zilon 
Author URI: 
Text Domain: -e('Zilon.io','Zilon_io')
 */
defined('ABSPATH') || exit('No Direct Access.');
define('ZILON_DIR', plugin_dir_path(__FILE__));
define('ZILON_URL', plugin_dir_url(__FILE__));
define('ZILONIO_CSS_URL', trailingslashit(ZILONIO_URL.'assets/css'));
define('ZILONIO_JS_URL', trailingslashit(ZILONIO_URL.'assets/js'));
define('ZILONIO_IMG_URL', trailingslashit(ZILONIO_URL.'assets/img'));
define('ZILONIO_INC_DIR', trailingslashit(ZILONIO_DIR.'include'));
define('ZILONIO_ADMIN_DIR', trailingslashit(ZILONIO_DIR.'admin'));
define('ZILONIO_TPL_DIR', trailingslashit(ZILONIO_DIR.'template'));
define('ZILONIO_VERSION', "0.1.0");


require ZILONIO_INC_DIR.'assets.php';
require ZILONIO_INC_DIR.'shortcodes.php';
add_action('plugins_loaded', 'ZILONIO_textdomain');
function ZILONIO_textdomain() {
	load_plugin_textdomain( 'Zilon_io', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
if(is_admin())
{
	require_once ZILONIO_ADMIN_DIR.'admin.php';
}
