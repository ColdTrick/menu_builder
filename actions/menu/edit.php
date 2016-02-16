<?php

$menu_name = get_input('menu_name'); // @todo check for valid menu name

if ($menu_name) {
	$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
	$menu->save();
	
	forward('admin/appearance/menu_items?menu_name=' . $menu_name);
}
