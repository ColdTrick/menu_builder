<?php

define("MENU_BUILDER_SUBTYPE", "menu_builder_menu_item");
define("MENU_BUILDER_ACCESS_LOGGED_OUT", -5);

require_once(dirname(__FILE__) . "/lib/functions.php");
require_once(dirname(__FILE__) . "/lib/hooks.php");
require_once(dirname(__FILE__) . "/lib/events.php");

/**
 * Init function for Menu Builder
 *
 * @return void
 */
function menu_builder_init() {
		
	elgg_extend_view("css/elgg", "css/menu_builder/site");
	elgg_extend_view("js/elgg", "js/menu_builder/site");
	
	// register pagehandler for nice URL's
	elgg_register_page_handler("menu_builder", "menu_builder_page_handler");
	
	// switch mode
	if (elgg_is_admin_logged_in()) {
		elgg_register_plugin_hook_handler("access:collections:write", "user", "menu_builder_write_access_hook");
		
		if (get_input("menu_builder_edit_mode") == "on") {
			elgg_load_js("lightbox");
			elgg_load_css("lightbox");
			
			$_SESSION["menu_builder_edit_mode"] = true;
		} elseif (get_input("menu_builder_edit_mode") == "off") {
			unset($_SESSION["menu_builder_edit_mode"]);
			unset($_SESSION["menu_builder_logged_out"]);
		}
		
		if (get_input("menu_builder_logged_out") == "on") {
			elgg_load_js("lightbox");
			elgg_load_css("lightbox");
			
			$_SESSION["menu_builder_logged_out"] = true;
		} elseif (get_input("menu_builder_logged_out") == "off") {
			unset($_SESSION["menu_builder_logged_out"]);
		}
	} else {
		unset($_SESSION["menu_builder_edit_mode"]);
		unset($_SESSION["menu_builder_logged_out"]);
	}
	
	// register url handler for menu_builder objects
	elgg_register_plugin_hook_handler("entity:url", "object", "menu_builder_menu_item_url_handler");
	
	// take control of menu setup
	elgg_unregister_plugin_hook_handler('prepare', 'menu:site', '_elgg_site_menu_setup');
	elgg_register_plugin_hook_handler('prepare', 'menu:site', 'menu_builder_site_menu_prepare');
	elgg_register_plugin_hook_handler('register', 'menu:site', 'menu_builder_site_menu_register');
}

/**
 * Page handler function for Menu Builder
 *
 * @param array $page requested page
 *
 * @return boolean
 */
function menu_builder_page_handler($page) {
	
	switch($page[0]){
		case "edit":
			if (!empty($page[1])) {
				set_input("guid", $page[1]);
			}
			
			include(dirname(__FILE__) . "/pages/edit.php");
			return true;
		case "reorder":
			include(dirname(__FILE__) . "/procedures/reorder.php");
			return true;
		default:
			return false;
	}
}

// register default Elgg events
elgg_register_event_handler("init", "system", "menu_builder_init");

elgg_register_event_handler("delete", "object", "menu_builder_delete_event_handler");

// register actions
elgg_register_action("menu_builder/edit", dirname(__FILE__) . "/actions/edit.php", "admin");
elgg_register_action("menu_builder/delete", dirname(__FILE__) . "/actions/delete.php", "admin");
elgg_register_action("menu_builder/reorder", dirname(__FILE__) . "/actions/reorder.php", "admin");
elgg_register_action("menu_builder/export", dirname(__FILE__) . "/actions/export.php", "admin");
elgg_register_action("menu_builder/import", dirname(__FILE__) . "/actions/import.php", "admin");
elgg_register_action("menu_builder/menu/edit", dirname(__FILE__) . "/actions/menu/edit.php", "admin");
	