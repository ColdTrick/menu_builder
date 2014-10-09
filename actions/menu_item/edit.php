<?php

$filter = true;
if (elgg_get_plugin_setting("htmlawed_filter", "menu_builder") == "no") {
	$filter = false;
}

$menu_name = get_input("menu_name");

$name = get_input("name");
$text = get_input("text", null, $filter);
$href = get_input("href", null, $filter);
$access_id = (int) get_input("access_id");
$target = get_input("target");
$is_action = get_input("is_action");
$parent_name = get_input("parent_name");
$priority = get_input("priority", time());

$managed_menus = menu_builder_get_managed_menus();

if (!in_array($menu_name, $managed_menus)) {
	register_error("Invalid menu name");
	forward(REFERER);
}

$current_config = json_decode(elgg_get_plugin_setting("menu_" . $menu_name . "_config", "menu_builder"), true);

if (!is_array($current_config)) {
	$current_config = array();
}

if (empty($name)) {
	$time = time();
	$name = "menu_name_" . $time;
	while (in_array($item_name, $current_config)) {
		$time++;
		$name = "menu_name_" . $time;
	}
} else {
	unset($current_config[$name]);
}

$menu_item = array(
	"text" => $text,
	"href" => $href,
	"access_id" => $access_id,
	"target" => $target,
	"is_action" => $is_action,
	"priority" => $priority,
	"parent_name" => $parent_name,
	"name" => $name
);

$current_config[$name] = $menu_item;

elgg_set_plugin_setting("menu_" . $menu_name . "_config", json_encode($current_config), "menu_builder");
elgg_reset_system_cache();
system_message("menu item saved");
forward(REFERER);
