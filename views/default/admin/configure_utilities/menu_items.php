<?php

elgg_require_js('admin/configure_utilities/menu_items');

$menus = menu_builder_get_managed_menus();

$selected = get_input('menu_name');
if (!empty($selected) && !in_array($selected, $menus)) {
	$selected = null;
}

$tabs = [];
if (!empty($menus)) {
	$menu_list = '';
	foreach ($menus as $menu) {
		if (empty($selected)) {
			$selected = $menu;
		}
		
		$menu_list .= elgg_view('menu_builder/admin/edit_menu', [
			'menu' => $menu,
			'class' => ($menu !== $selected) ? 'hidden' : null,
			'rel' => $menu,
		]);
		
		$tabs[] = [
			'text' => elgg_echo('menu:' . elgg_get_friendly_title($menu) . ':header:default'),
			'selected' => ($menu === $selected),
			'rel' => $menu,
			'href' => false,
		];
	}
} else {
	$menu_list = elgg_echo('notfound');
}

$menu = elgg_view('output/url', [
	'text' => elgg_echo('menu_builder:admin:menu:add'),
	'icon' => 'plus',
	'id' => 'menu-builder-add-menu',
	'href' => false,
	'class' => ['elgg-button', 'elgg-button-action'],
]);

$menu_list = elgg_view('navigation/tabs', ['tabs' => $tabs, 'class' => 'menu-builder-admin-tabs']) . $menu_list;

echo elgg_view_module('info', elgg_echo('menu_builder:admin:menu:list'), $menu_list, ['menu' => $menu]);
