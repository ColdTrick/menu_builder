<?php

/**
 * jQuery call to reorder menu items
 */

$menu_name = get_input('menu_name');
$item_name = get_input('item_name');
$parent_name = get_input('parent_name');
$items = get_input('items');

if (empty($item_name) || empty($item_name) || empty($items)) {
	register_error(elgg_echo('error:missing_data'));
	return;
}

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu_config = $menu->getMenuConfig();

if (empty($menu_config)) {
	register_error(elgg_echo('error:missing_data'));
	return;
}

$item_name_found = false;
$parent_name_found = false;

foreach ($menu_config as $key => $value) {
	// internal menu names get sanitized as class in menus, so look up matching items in config
	$sanitised_name = strtolower($key);
	$sanitised_name = str_replace('_', '-', $sanitised_name);
	$sanitised_name = str_replace(':', '-', $sanitised_name);
	$sanitised_name = str_replace(' ', '-', $sanitised_name);
	
	if ($item_name === $sanitised_name) {
		$item_name_found = true;
		$item_name = $key;
	}
	
	if (!empty($parent_name) && ($parent_name === $sanitised_name)) {
		$parent_name_found = true;
		$parent_name = $key;
	}
}

if (!$item_name_found) {
	register_error(elgg_echo('error:missing_data'));
	return;
}

if (!empty($parent_name) && !$parent_name_found) {
	register_error(elgg_echo('error:missing_data'));
	return;
}

if ($parent_name_found) {
	$menu_config[$item_name]['parent_name'] = $parent_name;
} else {
	unset($menu_config[$item_name]['parent_name']);
}

foreach ($menu_config as $key => $value) {
	// internal menu names get sanitized as class in menus, so look up matching items in config
	$sanitised_name = strtolower($key);
	$sanitised_name = str_replace('_', '-', $sanitised_name);
	$sanitised_name = str_replace(':', '-', $sanitised_name);
	$sanitised_name = str_replace(' ', '-', $sanitised_name);

	if (!in_array($sanitised_name, $items)) {
		continue;
	}
	
	$menu_config[$key]['priority'] = array_search($sanitised_name, $items);
}

$menu->setMenuConfig($menu_config);