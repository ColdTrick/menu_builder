<?php

$menu_name = get_input('menu_name'); // @todo check for valid menu name

if (!$menu_name) {
	return elgg_error_response(elgg_echo('menu_builder:actions:missing_name'));
}

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu->save();

return elgg_ok_response('', '', "admin/configure_utilities/menu_items?menu_name={$menu_name}");
