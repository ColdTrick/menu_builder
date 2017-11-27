<?php

$menu_name = get_input('menu_name');
if (!$menu_name) {
	register_error(elgg_echo('menu_builder:actions:missing_name'));
	forward(REFERER);
}

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);

$config = $menu->getMenuConfig();

// make sure we have a menu to export
if (empty($config)) {
	register_error(elgg_echo('menu_builder:actions:export:error:empty'));
	forward(REFERER);
}

$export_name = 'menu_builder_export_' . elgg_get_friendly_title($menu_name) . '.json';

// export the array as JSON in a txt file
$json = json_encode($config);

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');
header('Content-Length: ' . strlen($json));
header('Content-Disposition: attachment; filename="' . $export_name . '"');
echo $json;

exit;
