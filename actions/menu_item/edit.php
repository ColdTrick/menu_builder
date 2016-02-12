<?php

$menu_name = get_input('menu_name');

$managed_menus = menu_builder_get_managed_menus();

if (!in_array($menu_name, $managed_menus)) {
	register_error(elgg_echo('menu_builder:actions:edit:error:input'));
	forward(REFERER);
}

$filter = true;
if (elgg_get_plugin_setting('htmlawed_filter', 'menu_builder') == 'no') {
	$filter = false;
}

// add a default menu item
$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu->addMenuItem([
	'name' => get_input('name'),
	'text' => get_input('text', null, $filter),
	'href' => get_input('href', null, $filter),
	'access_id' => (int) get_input('access_id', ACCESS_PUBLIC),
	'target' => get_input('target'),
	'is_action' => get_input('is_action', false),
	'priority' => get_input('priority', time()),
	'parent_name' => get_input('parent_name'),
]);

system_message(elgg_echo('menu_builder:actions:edit:success'));
forward(REFERER);
