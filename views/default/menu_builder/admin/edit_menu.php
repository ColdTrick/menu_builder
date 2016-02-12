<?php

$menu = elgg_extract('menu', $vars);

elgg_push_context('menu_builder_manage');

$menu_items = elgg_view_menu($menu, [
	'show_section_headers' => true,
	'sort_by' => 'priority',
	'class' => 'elgg-divide-bottom pbm mbm menu-builder-manage clearfix',
]);

elgg_pop_context();

echo elgg_view('output/url', [
	'href' => 'action/menu_builder/export',
	'text' => elgg_echo('export'),
	'class' => 'elgg-button elgg-button-submit',
]);
echo elgg_view('output/url', [
	'href' => 'admin/menu_builder/import',
	'text' => elgg_echo('import'),
	'class' => 'elgg-button elgg-button-submit',
]);
echo elgg_view('output/url', [
	'href' => 'action/menu_builder/menu/delete?menu_name=' . $menu,
	'text' => elgg_echo('delete'),
	'confirm' => elgg_echo('question:areyousure'),
	'class' => 'elgg-button elgg-button-submit',
]);

echo elgg_format_element('div', ['class' => 'elgg-admin-sidebar-menu'], $menu_items);

