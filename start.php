<?php

define('MENU_BUILDER_ACCESS_LOGGED_OUT', -5);

require_once(dirname(__FILE__) . '/lib/functions.php');

// register default MyVox events
myvox_register_event_handler('init', 'system', 'menu_builder_init');

/**
 * Init function for Menu Builder
 *
 * @return void
 */
function menu_builder_init() {

	myvox_extend_view('css/admin', 'css/menu_builder/admin.css');
	
	// add our own css
	myvox_register_css('menu_builder_site', myvox_get_simplecache_url('css', 'css/menu_builder/site_menu.css'));
	
	// plugin hooks
	myvox_register_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareSiteMenu', 900);
	
	myvox_register_plugin_hook_handler('prepare', 'all', '\ColdTrick\MenuBuilder\MenuHooks::prepareMenuSetSelected', 9999);
	
	// events
	myvox_register_event_handler('ready', 'system', 'menu_builder_ready');
	myvox_register_event_handler('upgrade', 'system', '\ColdTrick\MenuBuilder\Upgrade::migrateEntitiesToJSON');
	
	// ajax views
	myvox_register_ajax_view('menu_builder/import');
	myvox_register_ajax_view('menu_builder/edit_item');
	
	// register actions
	myvox_register_action('menu_builder/regen_site_menu', dirname(__FILE__) . '/actions/regen_site_menu.php', 'admin');
	
	myvox_register_action('menu_builder/menu/reorder', dirname(__FILE__) . '/actions/menu/reorder.php', 'admin');
	myvox_register_action('menu_builder/menu/export', dirname(__FILE__) . '/actions/menu/export.php', 'admin');
	myvox_register_action('menu_builder/menu/import', dirname(__FILE__) . '/actions/menu/import.php', 'admin');
	
	myvox_register_action('menu_builder/menu/edit', dirname(__FILE__) . '/actions/menu/edit.php', 'admin');
	myvox_register_action('menu_builder/menu/delete', dirname(__FILE__) . '/actions/menu/delete.php', 'admin');
	myvox_register_action('menu_builder/menu_item/edit', dirname(__FILE__) . '/actions/menu_item/edit.php', 'admin');
	myvox_register_action('menu_builder/menu_item/delete', dirname(__FILE__) . '/actions/menu_item/delete.php', 'admin');
	
}

/**
 * System,ready function for Menu Builder
 *
 * @return void
 */
function menu_builder_ready() {

	if (menu_builder_is_managed_menu('site')) {
		// take control of menu setup
		myvox_unregister_plugin_hook_handler('prepare', 'menu:site', '_myvox_site_menu_setup');
	}
	
	$managed_menus = menu_builder_get_managed_menus();
	foreach ($managed_menus as $menu_name) {
		myvox_register_plugin_hook_handler('register', "menu:{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::registerAllMenu', 999);
		myvox_register_plugin_hook_handler('prepare', "menu:{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::prepareAllMenu', 999);
		
		if (!myvox_in_context('admin')) {
			// extend view for cache output
			myvox_extend_view("navigation/menu/{$menu_name}", 'menu_builder/menu_cache', 400);
			
			myvox_register_plugin_hook_handler('view', "navigation/menu/{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::afterViewMenu', 9999);
		}
	}
}
	