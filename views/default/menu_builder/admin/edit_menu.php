<?php

$menu = elgg_extract('menu', $vars);
unset($vars['menu']);

$vars['class'] = (array) elgg_extract('class', $vars, []);
$vars['class'][] = 'menu-builder-admin-menu';

elgg_push_context('menu_builder_manage');

$menu_items = elgg_view_menu($menu, [
	'sort_by' => 'priority',
	'class' => 'menu-builder-manage',
]);

elgg_pop_context();

$button_bank = elgg_view_menu('menu-builder-menu-actions', [
	'align' => 'right',
	'class' => ['elgg-menu-hz', 'mtm'],
	'item_class' => ['mrm'],
	'items' => [
		[
			'name' => 'export',
			'icon' => 'download',
			'text' => elgg_echo('export'),
			'class' => 'elgg-button elgg-button-submit',
			'href' => elgg_generate_action_url('menu_builder/menu/export', [
				'menu_name' => $menu,
			]),
		],
		[
			'name' => 'import',
			'icon' => 'upload',
			'text' => elgg_echo('import'),
			'class' => 'elgg-button elgg-button-submit elgg-lightbox',
			'href' => 'ajax/view/menu_builder/import?menu_name=' . $menu,
		],
		[
			'name' => 'delete',
			'icon' => 'delete',
			'text' => elgg_echo('delete'),
			'confirm' => true,
			'class' => 'elgg-button elgg-button-delete',
			'href' => elgg_generate_action_url('menu_builder/menu/delete', [
				'menu_name' => $menu,
			]),
		],
	],
]);

echo elgg_format_element('div', $vars, $menu_items . $button_bank);

