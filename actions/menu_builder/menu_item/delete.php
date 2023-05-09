<?php

$name = get_input('item_name');
$menu_name = get_input('menu_name');

if (elgg_is_empty($name) || elgg_is_empty($menu_name)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu_items = $menu->getMenuConfig();
if (empty($menu_items)) {
	return;
}

$menu->removeMenuItem($name);

return elgg_ok_response('', elgg_echo('menu_builder:actions:delete:success'), "admin/configure_utilities/menu_items?menu_name={$menu_name}");
