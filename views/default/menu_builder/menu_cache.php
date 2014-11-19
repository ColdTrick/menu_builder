<?php
$menu_name = $vars["name"];
$data = false;
if (!elgg_in_context("admin")) {
	$data = menu_builder_get_menu_cache($menu_name);
}

if ($data) {
	echo $data;
} elseif ($menu_name !== "site") {
	echo elgg_view("navigation/menu/default", $vars);
}

if (!$data) {
	// hook after view to save cache
	elgg_register_plugin_hook_handler("view", "navigation/menu/$menu_name", "menu_builder_view_menu_hook_handler", 999);
}