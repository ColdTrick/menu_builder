<?php

$name = get_input('item_name');
$menu_name = get_input('menu_name');

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu_items = $menu->getMenuConfig();
if (empty($menu_items)) {
	return;
}

$menu->removeMenuItem($name);

return elgg_ok_response('', elgg_echo('menu_builder:actions:delete:success'), "admin/configure_utilities/menu_items?menu_name={$menu_name}");
