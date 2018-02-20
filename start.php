<?php

define('MENU_BUILDER_ACCESS_LOGGED_OUT', -5);

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

/**
 * Returns an array of all the menu names that are managed by menu_builder
 *
 * @return array
 */
function menu_builder_get_managed_menus() {
	static $result;
	if (isset($result)) {
		return $result;
	}
	$result = json_decode(elgg_get_plugin_setting('menu_names', 'menu_builder'), true);
	return (array) $result;
}

/**
 * Checks if a menu is a managed menu
 *
 * @param string $menu_name name of the menu item to check
 *
 * @return bool
 */
function menu_builder_is_managed_menu($menu_name) {
	if (empty($menu_name)) {
		return false;
	}
	
	$menus = menu_builder_get_managed_menus();
	return in_array($menu_name, $menus);
}
	