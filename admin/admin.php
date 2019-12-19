<?php
add_action('admin_menu', 'Zilon_io_admin_menu');

function Zilon_io_admin_menu(){

	$main = add_menu_page(__('Zilon_io', 'Zilon_io'), __('Zilon_io', 'Zilon_io'), 'manage_options', 'Zilon_io_dashboard', 'Zilon_io_dashboard_page', ZILONIO_IMG_URL."logo.svg");

	$main_sub = add_submenu_page('Zilon_io_dashboard', __('Zilon_io', 'Zilon_io'), __('Zilon_io', 'Zilon_io'), 'manage_options', 'Zilon_io_dashboard');

}

function Zilon_io_dashboard_page ()
{
    require ZILONIO_TPL_DIR.'html-admin-main.php';
}
