<?php

myvox_require_js('menu_builder/manage_menu_items');

$menus = menu_builder_get_managed_menus();

$selected = get_input('menu_name');
if (!empty($selected) && !menu_builder_is_managed_menu($selected)) {
	$selected = null;
}

$tabs = [];
if (!empty($menus)) {
	
	$menu_list = '';
	foreach ($menus as $menu) {
		
		if (empty($selected)) {
			$selected = $menu;
		}
		
		$class = 'hidden';
		if ($menu === $selected) {
			$class = '';
		}
		
		$menu_list .= myvox_view('menu_builder/admin/edit_menu', ['menu' => $menu, 'class' => $class, 'rel' => $menu]);
		
		$tabs[] = [
			'text' => myvox_echo('menu:' . myvox_get_friendly_title($menu) . ':header:default'),
			'selected' => ($menu === $selected),
			'rel' => $menu,
			'href' => 'javascript:void(0)',
		];
	}
} else {
	$menu_list = myvox_echo('notfound');
}

$tabs[] = [
	'text' => ' <strong>+</strong> ' . myvox_echo('menu_builder:admin:menu:add'),
	'id' => 'menu-builder-add-menu',
	'href' => 'javascript:void(0)',
];

$menu_list = myvox_view('navigation/tabs', ['tabs' => $tabs, 'class' => 'menu-builder-admin-tabs']) . $menu_list;

echo myvox_view_module('inline', myvox_echo('menu_builder:admin:menu:list'), $menu_list);
