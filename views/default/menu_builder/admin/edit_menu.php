<?php

myvox_load_js('lightbox');
myvox_load_css('lightbox');

$menu = myvox_extract('menu', $vars);
unset($vars['menu']);

$vars['class'] = (array) myvox_extract('class', $vars, []);
$vars['class'][] = 'menu-builder-admin-menu';

myvox_push_context('menu_builder_manage');

$menu_items = myvox_view_menu($menu, [
	'sort_by' => 'priority',
	'class' => 'menu-builder-manage',
]);

myvox_pop_context();

$button_bank = myvox_view('output/url', [
	'href' => 'action/menu_builder/menu/export?menu_name=' . $menu,
	'is_action' => true,
	'text' => myvox_echo('export'),
	'class' => 'myvox-button myvox-button-submit',
]);
$button_bank .= myvox_view('output/url', [
	'href' => 'ajax/view/menu_builder/import?menu_name=' . $menu,
	'text' => myvox_echo('import'),
	'class' => 'myvox-button myvox-button-submit myvox-lightbox',
	'data-colorbox-opts' => json_encode(['width' => '50%']),
]);
$button_bank .= myvox_view('output/url', [
	'href' => 'action/menu_builder/menu/delete?menu_name=' . $menu,
	'text' => myvox_echo('delete'),
	'confirm' => myvox_echo('question:areyousure'),
	'class' => 'myvox-button myvox-button-submit',
]);

$button_bank = myvox_format_element('div', ['class' => 'menu-builder-admin-button-bank'], $button_bank);

echo myvox_format_element('div', $vars, $button_bank . $menu_items);

