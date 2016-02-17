<?php
$parent_options = elgg_extract('parent_options', $vars);
$menu_item = elgg_extract('menu_item', $vars);

$menu_name = $menu_item->menu_builder_menu_name;

if ($menu_item->getName() == 'menu_builder_add') {
	$text = '';
	$href = '';
	$access_id = ACCESS_PUBLIC;
	$target = 0;
	$is_action = false;
	$name = null;
	$parent_name = null;
	$priority = null;
	
	if (elgg_get_config('walled_garden')) {
		$access_id = ACCESS_LOGGED_IN;
	}
} else {
	$name = $menu_item->getName();
	$text = $menu_item->getText();
	
	$href = $menu_item->getHref();
	if (strpos($href, elgg_get_site_url()) === 0) {
		$href = substr($href, strlen(elgg_get_site_url()));
	}
	$access_id = $menu_item->access_id;
	$target = $menu_item->target;
	$is_action = (boolean) $menu_item->is_action;
	$parent_name = $menu_item->getParentName();
	$priority = $menu_item->getPriority();
}

$target_options = ['0' => elgg_echo('menu_builder:add:form:target:self'), '_blank' => elgg_echo('menu_builder:add:form:target:blank')];
$access_options = [
	ACCESS_PUBLIC => elgg_echo('PUBLIC'),
	ACCESS_LOGGED_IN => elgg_echo('LOGGED_IN'),
	MENU_BUILDER_ACCESS_LOGGED_OUT => elgg_echo('LOGGED_OUT'),
	ACCESS_PRIVATE => elgg_echo('menu_builder:add:access:admin_only'),
];

$form_body = '';

$form_body .= elgg_view('input/hidden', ['name' => 'name', 'value' => $name]);
$form_body .= elgg_view('input/hidden', ['name' => 'menu_name', 'value' => $menu_name]);
$form_body .= '<table><tr><td>';

$form_body .= elgg_format_element('label', [], elgg_echo('title'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/text', [
	'name' => 'text',
	'value' => $text,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:url'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/text', [
	'name' => 'href',
	'value' => $href,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:priority'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/text', [
	'name' => 'priority',
	'value' => $priority,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:access'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/access', [
	'name' => 'access_id',
	'value' => $access_id,
	'options_values' => $access_options,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:target'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/select', [
	'name' => 'target',
	'value' => $target,
	'options_values' => $target_options,
]);

if ($parent_options) {
	array_unshift($parent_options, '');
	$form_body .= '</td></tr><tr><td>';
	$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:form:parent'));
	$form_body .= '</td><td>';
	
	$form_body .= elgg_view('input/select', [
		'name' => 'parent_name',
		'value' => $parent_name,
		'options_values' => $parent_options,
	]);
}

$form_body .= '</td></tr><tr><td>';
$form_body .= elgg_format_element('label', [], elgg_echo('menu_builder:add:action:tokens'));
$form_body .= '</td><td>';

$form_body .= elgg_view('input/checkbox', [
	'name' => 'is_action',
	'value' => 1,
	'checked' => $is_action,
]);

$form_body .= '</td></tr></table>';

$form_body .= elgg_view('input/submit', ['value' => elgg_echo('save')]);

echo elgg_view('input/form', [
	'action' => 'action/menu_builder/menu_item/edit',
	'body' => $form_body,
	'class' => 'hidden',
]);