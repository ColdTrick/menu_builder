<?php
$menus = elgg_get_plugin_setting("menu_names", "menu_builder");

if ($menus) {
	$menus = json_decode($menus);
	elgg_push_context("menu_builder_manage");
	$menu_list = "<div class='elgg-admin-sidebar-menu'>";
	foreach ($menus as $menu) {
		$menu_list .= elgg_view_menu($menu, array(
				"show_section_headers" => true,
				"sort_by" => "priority",
				"class" => "elgg-divide-bottom pbm mbm menu-builder-manage clearfix"
		));
	}
	$menu_list .= "</div>";
	elgg_pop_context();
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

$menus = menu_builder_get_managed_menus();
if ($menus) {
	$delete_menu_form_body = elgg_view("input/select", array("name" => "menu_name", "options" => $menus));
	$delete_menu_form_body .= elgg_view("input/submit", array("value" => elgg_echo("delete"), "class" => "mtm elgg-button-submit"));
	
	$delete_menu_form = elgg_view("input/form", array(
		"action" => "action/menu_builder/menu/delete",
		"body" => $delete_menu_form_body,
		"class" => "menu-builder-menu-delete"
		
	));
	
	echo elgg_view_module("inline", elgg_echo("menu_builder:admin:menu:delete"), $delete_menu_form);
}	
