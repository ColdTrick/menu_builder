<?php

/**
 * Functions for Menu Builder
 */

/**
 * Returns an array of all the menu names that are managed by menu_builder
 *
 * @return array
 */
function menu_builder_get_managed_menus() {
	return json_decode(elgg_get_plugin_setting('menu_names', 'menu_builder'), true);
}



/**
 * Recursively deletes menu_items
 *
 * @param string $menu_name  name of the menu item to delete
 * @param array  $menu_items array of menu items
 * @return array
 */
function menu_builder_delete_menu_item($menu_name, &$menu_items) {
	
	if (empty($menu_name) || empty($menu_items)) {
		return;
	}
	
	unset($menu_items[$menu_name]);
	foreach ($menu_items as $key => $item) {
		if ($item['parent_name'] == $menu_name) {
			menu_builder_delete_menu_item($key, $menu_items);
		}
	}
}

