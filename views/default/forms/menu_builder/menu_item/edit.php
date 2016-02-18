<?php

$menu_item = elgg_extract('menu_item', $vars);

$target_options = [
	'0' => elgg_echo('menu_builder:add:form:target:self'),
	'_blank' => elgg_echo('menu_builder:add:form:target:blank'),
];
$access_options = [
	ACCESS_PUBLIC => elgg_echo('PUBLIC'),
	ACCESS_LOGGED_IN => elgg_echo('LOGGED_IN'),
	MENU_BUILDER_ACCESS_LOGGED_OUT => elgg_echo('LOGGED_OUT'),
	ACCESS_PRIVATE => elgg_echo('menu_builder:add:access:admin_only'),
];

$parent_options = elgg_extract('parent_options', $vars);

$href = elgg_extract('href', $menu_item);
if (strpos($href, elgg_get_site_url()) === 0) {
	$href = substr($href, strlen(elgg_get_site_url()));
}

$default_access_id = ACCESS_PUBLIC;
if (elgg_get_config('walled_garden')) {
	$default_access_id = ACCESS_LOGGED_IN;
}

$form_body = '';

$form_body .= elgg_view('input/hidden', ['name' => 'menu_name', 'value' => elgg_extract('menu_name', $vars)]);
$form_body .= elgg_view('input/hidden', ['name' => 'name', 'value' => elgg_extract('name', $menu_item)]);
$form_body .= elgg_view('input/hidden', ['name' => 'priority', 'value' => elgg_extract('priority', $menu_item, time())]);

$form_body .= '<table class="mbm"><tr><td>';

$form_body .= elgg_format_element('label', [], elgg_echo('title'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/text', [
	'name' => 'text',
	'value' => elgg_extract('text', $menu_item),
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:url'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/text', [
	'name' => 'href',
	'value' => $href,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:access'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/access', [
	'name' => 'access_id',
	'value' => elgg_extract('access_id', $menu_item, $default_access_id),
	'options_values' => $access_options,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:target'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/select', [
	'name' => 'target',
	'value' => elgg_extract('target', $menu_item, 0),
	'options_values' => $target_options,
]);

if ($parent_options) {
	array_unshift($parent_options, '');
	$form_body .= '</td></tr><tr><td>';
	$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:parent'));
	$form_body .= '</td><td>';

	$form_body .= elgg_view('input/select', [
		'name' => 'parent_name',
		'value' => elgg_extract('parent_name', $menu_item),
		'options_values' => $parent_options,
	]);
}

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:action:tokens'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/checkbox', [
	'name' => 'is_action',
	'value' => 1,
	'checked' => (bool) elgg_extract('is_action', $menu_item),
]);

$form_body .= '</td></tr></table>';

$form_body .= elgg_view('input/submit', ['value' => elgg_echo('save')]);

echo $form_body;