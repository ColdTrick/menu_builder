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
			
			$menu_item["href"] = menu_builder_normalize_href($menu_item["href"]);
			
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
	if (elgg_in_context("menu_builder_manage")) {
		
		$menu = $return["default"];
		$parent_options = menu_builder_get_parent_options($menu);
		
		menu_builder_prepare_menu_items_edit($menu, $parent_options);
	}
}

/**
 * Adds the menu items to the site menu
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_site_menu_register($hook, $type, $return, $params) {
	$result = array();

	$options = array(
		"type" => "object",
		"subtype" => MENU_BUILDER_SUBTYPE,
		"limit" => false
	);

	if (!elgg_is_logged_in()) {
		$options["wheres"] = array("e.access_id IN (" . ACCESS_PUBLIC . ", " . MENU_BUILDER_ACCESS_LOGGED_OUT . ")");
	}

	$entities = elgg_get_entities($options);

	if (empty($entities) && !elgg_get_plugin_setting("menu_builder_default_imported", "menu_builder") && elgg_is_admin_logged_in()) {
		// create default menu items

		$priority = 10;
		$more_guid = null;

		if (count($return) > 5) {
			// create more menu item
			$item = new ElggObject();
			$item->subtype = MENU_BUILDER_SUBTYPE;
			$item->owner_guid = elgg_get_site_entity()->getGUID();
			$item->container_guid = elgg_get_site_entity()->getGUID();

			$item->access_id = ACCESS_PUBLIC;

			$item->order = ($priority * count($return)) + 10;
			$item->title = elgg_echo("more");
			$item->url = "#";
			$item->parent_guid = 0;
			$item->save();

			$more_guid = $item->getGUID();
		}

		foreach ($return as $key => $menu_item) {
			$item = new ElggObject();
			$item->subtype = MENU_BUILDER_SUBTYPE;
			$item->owner_guid = elgg_get_site_entity()->getGUID();
			$item->container_guid = elgg_get_site_entity()->getGUID();

			$item->access_id = ACCESS_PUBLIC;

			if ($key >= 5) {
				$item->parent_guid = $more_guid;
			} else {
				$item->parent_guid = 0;
			}

			$item->order = $priority;

			$item->title = $menu_item->getText();
			$item->url = str_replace(elgg_get_site_url(), "[wwwroot]", $menu_item->getHref());

			$item->save();

			$priority += 10;
		}

		elgg_set_plugin_setting("menu_builder_default_imported", time(), "menu_builder");

		// fetch items again
		$entities = elgg_get_entities($options);
	}

	if ($entities) {
		$parent_guids = array();
		foreach ($entities as $entity) {
			$parent_guids[] = $entity->getGUID();
		}

		foreach ($entities as $entity) {
			$parent_guid = $entity->parent_guid;
			$skip = false;

			while ($parent_guid) {
				if (!in_array($parent_guid, $parent_guids)) {
					$skip = true;
					break;
				}
				$parent_guid = get_entity($parent_guid)->parent_guid;
			}

			if ($skip) {
				continue;
			}

			$title = $entity->title;
			if (isset($_SESSION["menu_builder_edit_mode"])) {
				$title = $title . elgg_view_icon("settings-alt", "menu-builder-edit-menu-item");
			}

			$url = $entity->getURL();
			if ($entity->is_action) {
				$url = elgg_add_action_tokens_to_url($entity->getURL());
			}
			$menu_options = array(
						"name" => $entity->getGUID(),
						"text" => $title,
						"href" => $url,
						"priority" => $entity->order,
						"id" => $entity->getGUID()
			);

			if ($entity->target == "_blank") {
				$menu_options["target"] = "_blank";
			}

			if (elgg_is_admin_logged_in()) {
				$item_class = "menu-builder-access-" . $entity->access_id;
				if (isset($_SESSION["menu_builder_edit_mode"])) {
					$item_class .= " menu-builder-menu-item-sortable";
				}
				$menu_options["item_class"] = $item_class;
			}

			if ($entity->parent_guid) {
				$menu_options["parent_name"] = $entity->parent_guid;
			}

			$result[] = ElggMenuItem::factory($menu_options);
		}
	}

	return $result;
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