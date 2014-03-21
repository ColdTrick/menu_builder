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
						"class" => "menu_builder_add_link",
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
