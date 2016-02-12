<?php

$name = get_input('name');
$menu_name = get_input('menu_name');

$menu_items = json_decode(elgg_get_plugin_setting("menu_{$menu_name}_config", 'menu_builder'), true);
if (empty($menu_items)) {
	return;
}

menu_builder_delete_menu_item($name, $menu_items);

elgg_set_plugin_setting("menu_{$menu_name}_config", json_encode($menu_items), 'menu_builder');
