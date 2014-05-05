<?php

$menu_name = get_input("menu_name"); // @todo check for valid menu name

if ($menu_name) {

	$menus = elgg_get_plugin_setting("menu_names", "menu_builder");
	$menus = json_decode($menus, true);
	if (!is_array($menus)) {
		$menus = array();
	}
	
	if (!in_array($menu_name, $menus)) {
		$menus[] = $menu_name;
	}
	
	elgg_set_plugin_setting("menu_names", json_encode($menus), "menu_builder");
	
}

