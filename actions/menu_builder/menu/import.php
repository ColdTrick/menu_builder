<?php

$menu_name = get_input('menu_name');

$file = elgg_get_uploaded_file('import');

if (empty($file)) {
	return elgg_error_response(elgg_echo('menu_builder:actions:import:error:upload'));
}

$config = json_decode(file_get_contents($file), true);

if (!is_array($config) || empty($config)) {
	return elgg_error_response(elgg_echo('menu_builder:actions:import:error:invalid:content'));
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
	
	if (!empty($item['title'])) {
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
		if (!in_array($key, ['name', 'text', 'href', 'access_id', 'target', 'is_action', 'priority', 'parent_name', 'icon'])) {
			unset($item[$key]);
		}
	}
	
	$menu->addMenuItem($item);
}

return elgg_ok_response('', elgg_echo('menu_builder:actions:import:complete'), "admin/configure_utilities/menu_items?menu_name={$menu_name}");
