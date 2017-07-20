<?php

$menu_name = get_input('menu_name');
$item_name = get_input('item_name');

if (empty($menu_name)) {
	echo elgg_echo('error:missing_data');
	return;
}

// pushing context to prevent the use of menu cache
elgg_push_context('admin');

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu_config = $menu->getMenuConfig();

$menu_item = elgg_extract($item_name, $menu_config, []);

$body_vars = [
	'menu_name' => $menu_name,
	'menu_item' => $menu_item,
	'parent_options' => $menu->getInputOptions($item_name),
];

echo elgg_view_form('menu_builder/menu_item/edit', [], $body_vars);

elgg_pop_context();
