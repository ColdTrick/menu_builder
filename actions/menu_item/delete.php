<?php

$name = get_input('item_name');
$menu_name = get_input('menu_name');

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu_items = $menu->getMenuConfig();
if (empty($menu_items)) {
	return;
}

menu_builder_delete_menu_item($name, $menu_items);

$menu->setMenuConfig($menu_items);

system_message(elgg_echo('menu_builder:actions:delete:success'));
forward("admin/appearance/menu_items?menu_name={$menu_name}");