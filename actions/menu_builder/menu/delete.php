<?php

$menu_name = get_input('menu_name');

if (elgg_is_empty($menu_name)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu->delete();
