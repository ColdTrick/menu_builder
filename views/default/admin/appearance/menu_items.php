<?php

elgg_require_js('menu_builder/manage_menu_items');

$menus = menu_builder_get_managed_menus();

$tabs = [];
if (!empty($menus)) {
	
	$menu_list = '';
	foreach ($menus as $menu) {
		$tabs[] = [
			'text' => $menu,
			'href' => 'javascript:void(0)',
		];
		$menu_list .= elgg_view('menu_builder/admin/edit_menu', ['menu' => $menu]);
	}
} else {
	$menu_list = elgg_echo('notfound');
}

$tabs[] = [
	'text' => ' + ' . elgg_echo('menu_builder:admin:menu:add'),
	'id' => 'menu-builder-add-menu',
	'href' => 'javascript:void(0)',
];

$menu_list = elgg_view('navigation/tabs', ['tabs' => $tabs]) . $menu_list;

echo elgg_view_module('inline', elgg_echo('menu_builder:admin:menu:list'), $menu_list);
