<?php

function menu_builder_get_toplevel_menu_items(){
	
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
 * @param array $menu_items the current level of menu items
 * @param int $entity_guid the current entity being edited (optional)
 * @param int $depth recursive depth for layout
 *
 * @return array the selection options
 */
function menu_builder_get_menu_select_option($menu_items, $entity_guid = 0, $depth = 0) {
	$result = array();
	
	$entity_guid = sanitise_int($entity_guid, false);
	
	if (!empty($menu_items)) {
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
