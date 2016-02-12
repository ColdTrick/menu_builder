<?php
$menus = elgg_get_plugin_setting('menu_names', 'menu_builder');

if ($menus) {
	$menus = json_decode($menus);
	elgg_push_context('menu_builder_manage');
	
	$menu_list = '';
	foreach ($menus as $menu) {
		$menu_list .= elgg_view_menu($menu, [
			'show_section_headers' => true,
			'sort_by' => 'priority',
			'class' => 'elgg-divide-bottom pbm mbm menu-builder-manage clearfix',
		]);
	}
	
	$menu_list = elgg_format_element('div', ['class' => 'elgg-admin-sidebar-menu'], $menu_list);
	elgg_pop_context();
} else {
	$menu_list = elgg_echo('notfound');
}

echo elgg_view_module('inline', elgg_echo('menu_builder:admin:menu:list'), $menu_list);

$add_menu_form_body = elgg_format_element('label', [], elgg_echo('menu_builder:admin:menu:add:internal_name'));
$add_menu_form_body .= elgg_view('input/text', ['name' => 'menu_name']);
$add_menu_form_body .= elgg_view('input/submit', ['value' => elgg_echo('save'), 'class' => 'mtm elgg-button-submit']);

$add_menu_form = elgg_view('input/form', [
	'action' => 'action/menu_builder/menu/edit',
	'body' => $add_menu_form_body,
]);
echo elgg_view_module('inline', elgg_echo('menu_builder:admin:menu:add'), $add_menu_form);

$menus = menu_builder_get_managed_menus();
if ($menus) {
	$delete_menu_form_body = elgg_view('input/select', [
		'name' => 'menu_name',
		'options' => $menus,
	]);
	$delete_menu_form_body .= elgg_view('input/submit', [
		'value' => elgg_echo('delete'),
		'data-confirm' => elgg_echo('question:areyousure'),
		'class' => 'mtm elgg-button-submit',
	]);
	
	$delete_menu_form = elgg_view('input/form', [
		'action' => 'action/menu_builder/menu/delete',
		'body' => $delete_menu_form_body,
	]);
	
	echo elgg_view_module('inline', elgg_echo('menu_builder:admin:menu:delete'), $delete_menu_form);
}

$export_import_form = elgg_view('output/url', [
	'href' => 'action/menu_builder/export',
	'text' => elgg_echo('export'),
	'class' => 'elgg-button elgg-button-submit',
]);
$export_import_form .= elgg_view('output/url', [
	'href' => 'admin/menu_builder/import',
	'text' => elgg_echo('import'),
	'class' => 'elgg-button elgg-button-submit',
]);

echo elgg_view_module('inline', elgg_echo('menu_builder:admin:menu:export_import'), $export_import_form);
