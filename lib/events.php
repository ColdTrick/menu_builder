<?php

/**
 * Event handlers for Menu Builder
 */

/**
 * Handles upgrades for the menu builder
 * 
 * @param string $event upgrade
 * @param string $type system
 * 
 * @return void
 */
function menu_builder_upgrade_event_handler($event, $type) {
	// Migrate pre 2.0 menu items to new json format
	menu_builder_add_menu("site");
	
	$options = array(
		"type" => "object",
		"subtype" => MENU_BUILDER_SUBTYPE,
		"limit" => false
	);
	
	$entities = elgg_get_entities($options);
	
	if (empty($entities)) {
		return;
	}
	
	foreach ($entities as $menu_item) {
		$parent_name = null;
		$parent_guid = $menu_item->parent_guid;
		if ($parent_guid) {
			$parent = get_entity($parent_guid);
			if ($parent) {
				$parent_name = "menu_name_" . $parent_guid;
			}
		}
		
		menu_builder_add_menu_item("site", array(
			"name" => "menu_name_" . $menu_item->guid,
			"text" => $menu_item->title,
			"href" => $menu_item->url,
			"target" => $menu_item->target,
			"is_action" => $menu_item->is_action,
			"access_id" => $menu_item->access_id,
			"priority" => $menu_item->order,
			"parent_name" => $parent_name
		));
	}
	

	// delete entities need to do it afterwards as parents are not always available otherwise
	foreach($entities as $menu_item) {
		$menu_item->delete();
	}
}
