<?php

/**
 * Hooks for Menu Builder
 */

/**
 * Adds the menu items to the menus managed by menu_builder
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_all_menu_register($hook, $type, $return, $params) {
	$current_menu = $params["name"];
	$return = array(); // need to reset as there should be no other way to add menu items
	
	if (!elgg_in_context("admin") && menu_builder_get_menu_cache($current_menu)) {
		// don't get menu as it will be handle by the cache @see menu_builder_view_navigation_menu_default_hook
		return $return;
	}
	
	// fix menu name if needed
	$lang_key = "menu:" . elgg_get_friendly_title($current_menu) . ":header:default";
	if (elgg_echo($lang_key) == $lang_key) {
		add_translation(get_current_language(), array($lang_key => $current_menu));
	}
	
	// add configured menu items
	$menu_items = json_decode(elgg_get_plugin_setting("menu_" . $current_menu . "_config", "menu_builder"), true);
	
	if (is_array($menu_items)) {
		foreach ($menu_items as $menu_item) {
			$can_add_menu_item = true;
			
			if (elgg_in_context("menu_builder_manage")) {
				$menu_item["menu_builder_menu_name"] = $current_menu;
			} else {
			
				if (empty($menu_item["target"])) {
					unset($menu_item["target"]);
				}
				
				$access_id = $menu_item["access_id"];
				unset($menu_item["access_id"]);
				switch($access_id) {
					case ACCESS_PRIVATE:
						if (!elgg_is_admin_logged_in()) {
							$can_add_menu_item = false;
						}
						break;
					case MENU_BUILDER_ACCESS_LOGGED_OUT:
						if (elgg_is_logged_in()) {
							$can_add_menu_item = false;
						}
						break;
					case ACCESS_LOGGED_IN:
						if (!elgg_is_logged_in()) {
							$can_add_menu_item = false;
						}
						break;
				}
			}
			
			// strip out deprecated use of [wwwroot] as menu items will be normalized by default
			$menu_item["href"] = str_replace("[wwwroot]", "", $menu_item["href"]);
			
			// add global replacable action tokens
			if ($menu_item["is_action"] && !elgg_in_context("menu_builder_manage")) {
				unset($menu_item["is_action"]);
				
				$concat = "?";
				if (stristr($menu_item["href"], "?")) {
					$concat = "&";
				}
				$menu_item["href"] .= $concat . "__elgg_ts=[__elgg_ts]&__elgg_token[__elgg_token]";
			}
			
			if (empty($menu_item['href'])) {
				$menu_item['href'] = false;
			}
						
			if ($can_add_menu_item) {
				$return[] = ElggMenuItem::factory($menu_item);
			}
		}
	}
	
	// add 'new menu item' menu item
	if (elgg_in_context("menu_builder_manage")) {
		$item = ElggMenuItem::factory(array(
			"name" => 'menu_builder_add',
			"text" => "<strong>+</strong>&nbsp;&nbsp;" . elgg_echo("menu_builder:edit_mode:add"),
			"href" => "#",
			"link_class" => "elgg-lightbox",
			"menu_builder_menu_name" => $current_menu,
			"priority" => time()
		));
		
		$return[] = $item;
	}
	
	return $return;
}


/**
 * Makes menus managable if needed
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_all_menu_prepare($hook, $type, $return, $params) {
	
	// update order
	$ordered = array();
	
	if (isset($return["default"])) {
		foreach ($return["default"] as $menu_item) {
	
			$menu_item = menu_builder_order_menu_item($menu_item, 2);
			$priority = $menu_item->getPriority();
			while (array_key_exists($priority, $ordered)) {
				$priority++;
			}
			$ordered[$priority] = $menu_item;
		}
	}
	
	ksort($ordered);
	
	$return["default"] = $ordered;
	
	// prepare menu items for edit
	if (elgg_in_context("menu_builder_manage")) {
		
		$menu = $return["default"];
		$parent_options = menu_builder_get_parent_options($menu);
		
		menu_builder_prepare_menu_items_edit($menu, $parent_options);
	}
	
	return $return;
}

/**
 * Caches menus
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return void
 */
function menu_builder_view_menu_hook_handler($hook, $type, $return, $params) {
	if (!elgg_in_context("admin")) {
		$cache_name = menu_builder_get_menu_cache_name($params["vars"]["name"]);
		elgg_save_system_cache($cache_name, $return);
	}
}

/**
 * Replaces dynamic data in menu's
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return void
 */
function menu_builder_view_menu_after_hook_handler($hook, $type, $return, $params) {
	if (empty($return)) {
		return $return;
	}

	// fill in username/userguid
	$user = elgg_get_logged_in_user_entity();
	if ($user) {
		$return = str_replace("[username]", $user->username, $return);
		$return = str_replace("[userguid]", $user->guid, $return);
	} else {
		$return = str_replace("[username]", "", $return);
		$return = str_replace("[userguid]", "", $return);
	}

	// add in tokens
	$elgg_ts = time();
	$elgg_token = generate_action_token($elgg_ts);
	
	$return = str_replace("[__elgg_ts]", $elgg_ts, $return);
	$return = str_replace("[__elgg_token]", $elgg_token, $return);
	
	return $return;
}

/**
 * Make sure all items are selected correctly
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_prepare_menu_set_selected_hook($hook, $type, $return, $params) {
	
	if (strpos($type, "menu:") !== 0) {
		return $return;
	}
	
	if (empty($params) || !is_array($params)) {
		return $return;
	}
	
	// set selected state on parent menu items
	$item = elgg_extract("selected_item", $params);
	if (empty($item) || !($item instanceof ElggMenuItem)) {
		return $return;
	}
	
	while ($item && ($item = $item->getParent())) {
		$item->setSelected(true);
	}
}

/**
 * Loads initially the site menu into the menu_builder config.
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_site_menu_prepare($hook, $type, $return, $params) {
	if (elgg_get_plugin_setting("menu_builder_default_imported", "menu_builder", false)) {
		return;
	}
	
	menu_builder_add_menu("site");

	$priority = 10;
	$parent_name = null;

	foreach ($return as $section => $items) {
		$parent_name = null;
		
		if ($section !== "default") {
			menu_builder_add_menu_item("site", array(
				"name" => $section,
				"text" => elgg_echo($section),
				"href" => "#",
				"priority" => $priority,
			));
				
			$parent_name = $section;
			$priority += 10;
		}

		foreach ($items as $item) {
			menu_builder_add_menu_item("site", array(
				"name" => $item->getName(),
				"text" => $item->getText(),
				"href" => str_replace(elgg_get_site_url(), "", $item->getHref()),
				"priority" => $priority,
				"parent_name" => $parent_name
			));
				
			$priority += 10;
		}
	}

	elgg_set_plugin_setting("menu_builder_default_imported", time(), "menu_builder");
}
