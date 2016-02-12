<?php

$menu_name = get_input('menu_name');

$menus = menu_builder_get_managed_menus();

if (!in_array($menu_name, $menus)) {
	return;
}

$key = array_search($menu_name, $menus);
unset($menus[$key]);

elgg_set_plugin_setting('menu_names', json_encode($menus), 'menu_builder');
elgg_unset_plugin_setting("menu_{$menu_name}_config", 'menu_builder');
