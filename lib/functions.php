<?php

/**
 * Functions for Menu Builder
 */

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
		$ordered_children = [];

		foreach ($children as $child) {

			$child = menu_builder_order_menu_item($child, $depth + 1);

			$child_priority = $child->getPriority();
			while (array_key_exists($child_priority, $ordered_children)) {
				$child_priority++;
			}
			$ordered_children[$child_priority] = $child;
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
	return json_decode(elgg_get_plugin_setting('menu_names', 'menu_builder'), true);
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
		$text = $menu_item->getText();
		
		if ($menu_item->getName() !== 'menu_builder_add') {
			$text .= elgg_format_element('span', ['title' => elgg_echo('edit')], elgg_view_icon('settings-alt'));
			$text .= elgg_format_element('span', ['title' => elgg_echo('delete')], elgg_view_icon('delete'));
		}
		$text = elgg_view('output/url', ['href' => '#', 'text' => $text]);
			
		$text .= elgg_view('menu_builder/edit_item', ['menu_item' => $menu_item, 'parent_options' => $parent_options]);
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
	$result = [];
	foreach ($menu as $menu_item) {
		if ($menu_item->getName() == 'menu_builder_add') {
			continue;
		}
		$text = str_repeat('-', $indent) . $menu_item->getText();
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
	$cache_name = "{$menu_name}_logged_in";
	if (!elgg_is_logged_in()) {
		$cache_name = "{$menu_name}_logged_out";
	} elseif (elgg_is_admin_logged_in()) {
		$cache_name = "{$menu_name}_admin";
	}
	
	return $cache_name;
}

/**
 * Adds a menu to the list of manageable menus
 *
 * @param string $menu_name name of the menu
 *
 * @return void
 */
function menu_builder_add_menu($menu_name) {
	$menus = elgg_get_plugin_setting('menu_names', 'menu_builder');
	$menus = json_decode($menus, true);
	if (!is_array($menus)) {
		$menus = [];
	}
	
	if (in_array($menu_name, $menus)) {
		// already exists
		return;
	}
	
	$menus[] = $menu_name;
	
	elgg_set_plugin_setting('menu_names', json_encode($menus), 'menu_builder');
	elgg_reset_system_cache();
}

/**
 * Adds a menu item to the list of manageable menu items
 *
 * @param string $menu_name name of the menu
 * @param array  $params    name of the menu
 *
 * @return void
 */
function menu_builder_add_menu_item($menu_name, array $params = []) {
	$filter = true;
	if (elgg_get_plugin_setting('htmlawed_filter', 'menu_builder') == 'no') {
		$filter = false;
	}
	
	$defaults = [
		'name' => get_input('name'),
		'text' => get_input('text', null, $filter),
		'href' => get_input('href', null, $filter),
		'access_id' => (int) get_input('access_id', ACCESS_PUBLIC),
		'target' => get_input('target'),
		'is_action' => get_input('is_action', false),
		'priority' => get_input('priority', time()),
		'parent_name' => get_input('parent_name'),
	];
		
	$menu_item = array_merge($defaults, $params);
	
	$current_config = json_decode(elgg_get_plugin_setting("menu_{$menu_name}_config", 'menu_builder'), true);
	
	if (!is_array($current_config)) {
		$current_config = [];
	}
	
	$name = elgg_extract('name', $menu_item);
	if (empty($name)) {
		$time = time();
		$name = "menu_name_{$time}";
		while (in_array($name, $current_config)) {
			$time++;
			$name = "menu_name_{$time}";
		}
		
		$menu_item['name'] = $name;
	}
	
	$current_config[$name] = $menu_item;
	
	elgg_set_plugin_setting("menu_{$menu_name}_config", json_encode($current_config), 'menu_builder');
	elgg_reset_system_cache();
}
