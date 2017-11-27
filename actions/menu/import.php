<?php

$menu_name = get_input('menu_name');

$contents = get_uploaded_file('import');

if (empty($contents)) {
	register_error(elgg_echo('menu_builder:actions:import:error:upload'));
	forward(REFERER);
}

$config = json_decode($contents, true);

if (!is_array($config) || empty($config)) {
	register_error(elgg_echo('menu_builder:actions:import:error:invalid:content'));
	forward(REFERER);
}

// assume we're good at this point, delete current menu
$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu->setMenuConfig(); // removes the current config
foreach ($config as $item) {
	// convert old export to new format
	if (isset($item['guid'])) {
		$item['name'] = $item['guid'];
		unset($item['guid']);
	}
	if (isset($item['title'])) {
		$item['text'] = $item['title'];
		unset($item['title']);
	}
	if (isset($item['url'])) {
		$item['href'] = $item['url'];
		unset($item['url']);
	}
	if (isset($item['order'])) {
		$item['priority'] = $item['order'];
		unset($item['order']);
	}
	if (isset($item['parent_guid'])) {
		$item['parent_name'] = $item['parent_guid'];
		unset($item['parent_guid']);
	}
	
	// only import supported data
	foreach ($item as $key => $value) {
		if (!in_array($key, ['name', 'text', 'href', 'access_id', 'target', 'is_action', 'priority', 'parent_name'])) {
			unset($item[$key]);
		}
	}
	
	$menu->addMenuItem($item);
}

system_message(elgg_echo('menu_builder:actions:import:complete'));
forward('admin/appearance/menu_items?menu_name=' . $menu_name);