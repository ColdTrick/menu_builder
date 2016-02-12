<?php

define('MENU_BUILDER_ACCESS_LOGGED_OUT', -5);

require_once(dirname(__FILE__) . '/lib/functions.php');

/**
 * Init function for Menu Builder
 *
 * @return void
 */
function menu_builder_init() {
		
// 	elgg_extend_view('css/elgg', 'css/menu_builder/site.css');
	elgg_extend_view('css/admin', 'css/menu_builder/admin.css');
// 	elgg_extend_view('js/elgg', 'js/menu_builder/site.js');
		
	// take control of menu setup
	elgg_unregister_plugin_hook_handler('prepare', 'menu:site', '_elgg_site_menu_setup');
	
	elgg_register_plugin_hook_handler('prepare', 'all', '\ColdTrick\MenuBuilder\MenuHooks::prepareMenuSetSelected', 9999);
	
	elgg_register_event_handler('pagesetup', 'system', 'menu_builder_pagesetup');
	elgg_register_event_handler('upgrade', 'system', '\ColdTrick\MenuBuilder\Upgrade::migrateEntitiesToJSON');
}

/**
 * Page setup function for Menu Builder
 *
 * @return void
 */
function menu_builder_pagesetup() {

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

// register default Elgg events
elgg_register_event_handler('init', 'system', 'menu_builder_init');

// register actions
// elgg_register_action('menu_builder/reorder', dirname(__FILE__) . '/actions/reorder.php', 'admin');
// elgg_register_action('menu_builder/export', dirname(__FILE__) . '/actions/export.php', 'admin');
// elgg_register_action('menu_builder/import', dirname(__FILE__) . '/actions/import.php', 'admin');

elgg_register_action('menu_builder/menu/edit', dirname(__FILE__) . '/actions/menu/edit.php', 'admin');
elgg_register_action('menu_builder/menu/delete', dirname(__FILE__) . '/actions/menu/delete.php', 'admin');
elgg_register_action('menu_builder/menu_item/edit', dirname(__FILE__) . '/actions/menu_item/edit.php', 'admin');
elgg_register_action('menu_builder/menu_item/delete', dirname(__FILE__) . '/actions/menu_item/delete.php', 'admin');
	