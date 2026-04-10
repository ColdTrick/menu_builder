<?php

$menu_name = get_input('menu_name');
if (!$menu_name) {
	return elgg_error_response(elgg_echo('menu_builder:actions:missing_name'));
}

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);

$config = $menu->getMenuConfig();

// make sure we have a menu to export
if (empty($config)) {
	return elgg_error_response(elgg_echo('menu_builder:actions:export:error:empty'));
}

$export_name = 'menu_builder_export_' . elgg_get_friendly_title($menu_name) . '.json';

return elgg_download_response(json_encode($config), $export_name);
