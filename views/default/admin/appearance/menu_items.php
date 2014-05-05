<?php
$menus = elgg_get_plugin_setting("menu_names", "menu_builder");

if ($menus) {
	$menus = json_decode($menus);
	foreach ($menus as $menu) {
		$menu_list .= elgg_view_menu($menu, array("show_section_headers" => true));
	}
} else {
	$menu_list = elgg_echo("notfound");
}

echo elgg_view_module("inline", elgg_echo("menu_builder:admin:menu:list"), $menu_list);


$add_menu_form_body = "<label>" . elgg_echo("menu_builder:admin:menu:add:internal_name") . "</label>";
$add_menu_form_body .= elgg_view("input/text", array("name" => "menu_name"));
$add_menu_form_body .= elgg_view("input/submit", array("value" => elgg_echo("save"), "class" => "mtm elgg-button-submit"));

$add_menu_form = elgg_view("input/form", array(
	"action" => "action/menu_builder/menu/edit",
	"body" => $add_menu_form_body,
	
));
echo elgg_view_module("inline", elgg_echo("menu_builder:admin:menu:add"), $add_menu_form);
