<?php

$menu = elgg_extract('menu', $vars);
unset($vars['menu']);

$vars['class'] = (array) elgg_extract('class', $vars, []);
$vars['class'][] = 'menu-builder-admin-menu';

elgg_push_context('menu_builder_manage');

$menu_items = elgg_view_menu($menu, [
	'sort_by' => 'priority',
	'class' => 'elgg-admin-sidebar-menu menu-builder-manage',
]);

elgg_pop_context();

$button_bank = elgg_view('output/url', [
	'href' => 'action/menu_builder/export',
	'text' => elgg_echo('export'),
	'class' => 'elgg-button elgg-button-submit',
]);
$button_bank .= elgg_view('output/url', [
	'href' => 'admin/menu_builder/import',
	'text' => elgg_echo('import'),
	'class' => 'elgg-button elgg-button-submit',
]);
$button_bank .= elgg_view('output/url', [
	'href' => 'action/menu_builder/menu/delete?menu_name=' . $menu,
	'text' => elgg_echo('delete'),
	'confirm' => elgg_echo('question:areyousure'),
	'class' => 'elgg-button elgg-button-submit',
]);

$button_bank = elgg_format_element('div', ['class' => 'menu-builder-admin-button-bank'], $button_bank);

echo elgg_format_element('div', $vars, $button_bank . $menu_items);
