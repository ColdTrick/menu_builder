<?php

elgg_require_js('menu_builder/manage_menu_items');

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
		
		$menu_list .= elgg_view('menu_builder/admin/edit_menu', ['menu' => $menu, 'class' => $class, 'rel' => $menu]);
		
		$tabs[] = [
			'text' => elgg_echo('menu:' . elgg_get_friendly_title($menu) . ':header:default'),
			'selected' => ($menu === $selected),
			'rel' => $menu,
			'href' => 'javascript:void(0)',
		];
	}
} else {
	$menu_list = elgg_echo('notfound');
}

$tabs[] = [
	'text' => ' <strong>+</strong> ' . elgg_echo('menu_builder:admin:menu:add'),
	'id' => 'menu-builder-add-menu',
	'href' => 'javascript:void(0)',
];

$menu_list = elgg_view('navigation/tabs', ['tabs' => $tabs, 'class' => 'menu-builder-admin-tabs']) . $menu_list;

echo elgg_view_module('inline', elgg_echo('menu_builder:admin:menu:list'), $menu_list);
