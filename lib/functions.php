<?php

/**
 * Functions for Menu Builder
 */

/**
 * Returns the toplevel menu items
 *
 * @return boolean|array
 */
function menu_builder_get_toplevel_menu_items() {

	$result = false;

	$options = array(
			"type" => "object",
			"subtype" => MENU_BUILDER_SUBTYPE,
			"owner_guid" => elgg_get_site_entity()->getGUID(),
			"limit" => false,
			"metadata_name" => "parent_guid",
			"metadata_value" => 0,
			"order_by_metadata" => array(
				"name" => "order",
				"direction" => "ASC",
				"as" => "integer")
	);

	if ($items = elgg_get_entities_from_metadata($options)) {
		$result = array();

		foreach ($items as $item) {
			$result[$item->getGUID()] = $item->title;
		}
	}

	return $result;
}

/**
 * Build the select options for the edit form
 *
 * @param int $entity_guid the current entity being edited (optional)
 *
 * @return array the parent select dropdown options
 */
function menu_builder_get_parent_menu_select_options($entity_guid = 0) {
	$result = false;

	$entity_guid = sanitise_int($entity_guid, false);

	// build the menu structure
	$vars = array();
	$menu = array();

	$menu = elgg_trigger_plugin_hook("register", "menu:site", $vars, $menu);

	$builder = new ElggMenuBuilder($menu);
	$vars["menu"] = $builder->getMenu("text");
	$vars["selected_item"] = $builder->getSelected();

	// Let plugins modify the menu
	$vars["menu"] = elgg_trigger_plugin_hook("prepare", "menu:site", $vars, $vars["menu"]);

	if (!empty($vars["menu"])) {
		$result = array();

		foreach ($vars["menu"] as $section => $menu_items) {

			if (!empty($menu_items) && is_array($menu_items)) {
				$result = menu_builder_get_menu_select_option($menu_items, $entity_guid);
			}
		}

	}

	return $result;
}

/**
 * Recursively loop through all menu items and children to get correct options.
 *
 * @param array $menu_items  the current level of menu items
 * @param int   $entity_guid the current entity being edited (optional)
 * @param int   $depth       recursive depth for layout
 *
 * @return array the selection options
 */
function menu_builder_get_menu_select_option($menu_items, $entity_guid = 0, $depth = 0) {
	$result = array();

	$entity_guid = sanitise_int($entity_guid, false);
	$depth = sanitise_int($depth, false);
	
	if (!empty($menu_items) && ($depth < 4)) {

		foreach ($menu_items as $menu_item) {
			$name = $menu_item->getName();

			if (!is_numeric($name)) {
				// skip extra menu items
				continue;
			}

			if (!empty($entity_guid) && ($name == $entity_guid)) {
				// skip yourself and all your children
				continue;
			}

			$result[$name] = trim(str_repeat("-", $depth) . " " . $menu_item->getText());

			$children = $menu_item->getChildren();
			if (!empty($children)) {
				$child_items = menu_builder_get_menu_select_option($children, $entity_guid, $depth + 1);

				if (!empty($child_items)) {
					$result += $child_items;
				}
			}
		}
	}

	return $result;
}

/**
 * Reorders menu item and adds an add button
 *
 * @param ElggMenuItem $item  menu item
 * @param int          $depth depth of the menu item
 *
 * @return ElggMenuItem
 */
function menu_builder_order_menu_item(ElggMenuItem $item, $depth) {

	$depth = (int) $depth;

	if ($children = $item->getChildren()) {
		// sort children
		$ordered_children = array();

		foreach ($children as $child) {

			$child = menu_builder_order_menu_item($child, $depth + 1);

			$child_priority = $child->getPriority();
			while (array_key_exists($child_priority, $ordered_children)) {
				$child_priority++;
			}
			$ordered_children[$child_priority] = $child;


			if (isset($_SESSION["menu_builder_edit_mode"]) && $depth < 5) {
				// add button
				$child_add = ElggMenuItem::factory(array(
						"name" => 'menu_builder_add',
						"text" => elgg_view_icon("round-plus"),
						"href" => '/menu_builder/edit?parent_guid=' . $child->getName(),
						"link_class" => "center elgg-lightbox",
						"title" => elgg_echo("menu_builder:edit_mode:add")
				));
				$child->addChild($child_add);
			}
		}
		ksort($ordered_children);

		$item->setChildren($ordered_children);
	}

	return $item;
}

/**
 * Returns an array of all the menu names that are managed by menu_builder
 * 
 * @return array
 */
function menu_builder_get_managed_menus() {
	return json_decode(elgg_get_plugin_setting("menu_names", "menu_builder"), true);
}

/**
 * Normalizes the href and replaces some parts of it
 * 
 * @param string $href current href
 * 
 * @return string
 */
function menu_builder_normalize_href($href) {
	if (empty($href)) {
		// empty href's should not have a href set
		return false;
	}

	// fill in site url
	$href = str_replace("[wwwroot]", elgg_get_site_url(), $href);

	// fill in username/userguid
	$user = elgg_get_logged_in_user_entity();
	if ($user) {
		$href = str_replace("[username]", $user->username, $href);
		$href = str_replace("[userguid]", $user->guid, $href);
	} else {
		list($href) = explode("[username]", $href);
		list($href) = explode("[userguid]", $href);
	}

	return $href;
}

/**
 * Prepares menu items to be edited
 * 
 * @param array $menu           array of ElggMenuItem objects
 * @param array $parent_options all parent options
 * 
 * @return void
 */
function menu_builder_prepare_menu_items_edit($menu, $parent_options) {
	foreach ($menu as $menu_item) {
		$text = "<a href='#'>" . $menu_item->getText();
		if ($menu_item->getName() != "menu_builder_add") {
			$text .= " <span title='" . elgg_echo("edit") . "'>" . elgg_view_icon("settings-alt") . "</span>";
			$text .= " <span title='" . elgg_echo("delete") . "'>" . elgg_view_icon("delete") . "</span>";
		}
		$text .= "</a>";
			
		$text .= elgg_view("menu_builder/edit_item", array("menu_item" => $menu_item, "parent_options" => $parent_options));
		$menu_item->setText($text);
		$menu_item->setHref(false);

		$children = $menu_item->getChildren();
		if ($children) {
			menu_builder_prepare_menu_items_edit($children, $parent_options);
		}
	}
}

/**
 * Returns an array of parent items to be used in edit forms of menu items
 * 
 * @param array $menu   array of ElggMenuItem objects
 * @param int   $indent number of indents 
 * 
 * @return array
 */
function menu_builder_get_parent_options($menu, $indent = 0) {
	$result = array();
	foreach ($menu as $menu_item) {
		if ($menu_item->getName() == "menu_builder_add") {
			continue;
		}
		$text = str_repeat("-", $indent) . $menu_item->getText();
		$result[$menu_item->getName()] = $text;
		$children = $menu_item->getChildren();
		if ($children) {
			$children_options = menu_builder_get_parent_options($children, $indent + 1);
			$result = $result + $children_options;
		}
	}

	return $result;
}

/**
 * Recursively deletes menu_items
 * 
 * @param string $menu_name  name of the menu item to delete
 * @param array  $menu_items array of menu items
 * @return array
 */
function menu_builder_delete_menu_item($menu_name, &$menu_items) {
	if (!empty($menu_name) && !empty($menu_items)) {
		unset($menu_items[$menu_name]);
		foreach ($menu_items as $key => $item) {
			if ($item["parent_name"] == $menu_name) {
				menu_builder_delete_menu_item($key, $menu_items);
			}
		}
	}
}

/**
 * Checks if cached menu html is available and returns the html if it is available
 * 
 * @param string $menu_name name of the menu
 * 
 * @return boolean|string
 */
function menu_builder_get_menu_cache($menu_name) {
	global $CONFIG;
	
	if (!$CONFIG->system_cache_enabled) {
		return false;	
	}
	
	$cache_name = menu_builder_get_menu_cache_name($menu_name);
	
	$data = elgg_load_system_cache($cache_name);
	
	if (!$data) {
		return false;
	}
	
	return $data;
}

/**
 * Returns name for menu cache file
 * 
 * @param string $menu_name name of the menu
 * 
 * @return string
 */
function menu_builder_get_menu_cache_name($menu_name) {
	$cache_name = $menu_name . "_logged_in";
	if (!elgg_is_logged_in()) {
		$cache_name = $menu_name . "_logged_out";
	} elseif (elgg_is_admin_logged_in()) {
		$cache_name = $menu_name . "_admin";
	}
	
	return $cache_name;
}
