<?php

define('MENU_BUILDER_ACCESS_LOGGED_OUT', -5);

require_once(dirname(__FILE__) . '/lib/functions.php');

// register default Elgg events
elgg_register_event_handler('init', 'system', 'menu_builder_init');

/**
 * Init function for Menu Builder
 *
 * @return void
 */
function menu_builder_init() {

	elgg_extend_view('css/admin', 'css/menu_builder/admin.css');

	// plugin hooks
	elgg_register_plugin_hook_handler('prepare', 'all', '\ColdTrick\MenuBuilder\MenuHooks::prepareMenuSetSelected', 9999);
	elgg_register_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareSiteMenu', 900);
	
	// events
	elgg_register_event_handler('ready', 'system', 'menu_builder_ready');
	
	// ajax views
	elgg_register_ajax_view('menu_builder/import');
	elgg_register_ajax_view('menu_builder/edit_item');
}

/**
 * System,ready function for Menu Builder
 *
 * @return void
 */
function menu_builder_ready() {

	if (menu_builder_is_managed_menu('site')) {
		// take control of menu setup
		elgg_unregister_plugin_hook_handler('prepare', 'menu:site', '_elgg_site_menu_setup');
	}
	
	$managed_menus = menu_builder_get_managed_menus();
	foreach ($managed_menus as $menu_name) {
		elgg_register_plugin_hook_handler('register', "menu:{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::registerAllMenu', 999);
		elgg_register_plugin_hook_handler('prepare', "menu:{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::prepareAllMenu', 999);
		
		if (!elgg_in_context('admin')) {
			// extend view for cache output
			elgg_extend_view("navigation/menu/{$menu_name}", 'menu_builder/menu_cache', 400);
			
			elgg_register_plugin_hook_handler('view', "navigation/menu/{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::afterViewMenu', 9999);
		}
	}
}
	