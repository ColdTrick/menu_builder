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
	
	elgg_extend_view("navigation/menu/site", "menu_builder/site_menu_extend");
		
	elgg_extend_view("css/elgg", "menu_builder/css/site");
	
	// register pagehandler for nice URL's
	elgg_register_page_handler("menu_builder", "menu_builder_page_handler");
	
	// switch mode
	if (elgg_is_admin_logged_in()) {
		elgg_register_plugin_hook_handler("access:collections:write", "user", "menu_builder_write_access_hook");
		
		if (get_input("menu_builder_edit_mode") == "on") {
			$_SESSION["menu_builder_edit_mode"] = true;
		} elseif (get_input("menu_builder_edit_mode") == "off") {
			unset($_SESSION["menu_builder_edit_mode"]);
			unset($_SESSION["menu_builder_logged_out"]);
		}
		
		if (get_input("menu_builder_logged_out") == "on") {
			$_SESSION["menu_builder_logged_out"] = true;
		} elseif (get_input("menu_builder_logged_out") == "off") {
			unset($_SESSION["menu_builder_logged_out"]);
		}
	} else {
		unset($_SESSION["menu_builder_edit_mode"]);
		unset($_SESSION["menu_builder_logged_out"]);
	}
	
	// register url handler for menu_builder objects
	elgg_register_entity_url_handler("object", MENU_BUILDER_SUBTYPE,"menu_builder_menu_item_url_handler");
	
	// take control of menu setup
	elgg_unregister_plugin_hook_handler('prepare', 'menu:site', 'elgg_site_menu_setup');
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

/**
 * Page setup function for Menu Builder
 *
 * @return void
 */
function menu_builder_pagesetup() {
	// no need for a seperate admin page to manage menu items TODO: replace page with a notice
	elgg_unregister_menu_item("page", "appearance:menu_items");
}
	
/**
 * Item url handler for Menu Builder
 *
 * @param ElggEntity $entity menu item
 *
 * @return string
 */
function menu_builder_menu_item_url_handler($entity) {
	$result = "javascript:void(0);";
	
	if ($url = $entity->url) {
		// fill in site url
		$url = str_replace("[wwwroot]", elgg_get_site_url(), $url);
		
		// fill in username/userguid
		$user = elgg_get_logged_in_user_entity();
		if ($user) {
			$url = str_replace("[username]", $user->username, $url);
			$url = str_replace("[userguid]", $user->getGUID(), $url);
		} else {
			list($url) = explode("[username]", $url);
			list($url) = explode("[userguid]", $url);
		}
		
		$result = $url;
	}
	
	return $result;
}

// register default Elgg events
elgg_register_event_handler("init", "system", "menu_builder_init");
elgg_register_event_handler("pagesetup", "system", "menu_builder_pagesetup");

elgg_register_event_handler("delete", "object", "menu_builder_delete_event_handler");

// register actions
elgg_register_action("menu_builder/edit", dirname(__FILE__) . "/actions/edit.php", "admin");
elgg_register_action("menu_builder/delete", dirname(__FILE__) . "/actions/delete.php", "admin");
elgg_register_action("menu_builder/reorder", dirname(__FILE__) . "/actions/reorder.php", "admin");
elgg_register_action("menu_builder/export", dirname(__FILE__) . "/actions/export.php", "admin");
elgg_register_action("menu_builder/import", dirname(__FILE__) . "/actions/import.php", "admin");
	