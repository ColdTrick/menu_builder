<?php
$menu_name = myvox_extract('menu_name', $vars, []);
$menu_item = myvox_extract('menu_item', $vars, []);

$target_options = [
	'0' => myvox_echo('menu_builder:add:form:target:self'),
	'_blank' => myvox_echo('menu_builder:add:form:target:blank'),
];
$access_options = [
	ACCESS_PUBLIC => myvox_echo('PUBLIC'),
	ACCESS_LOGGED_IN => myvox_echo('LOGGED_IN'),
	MENU_BUILDER_ACCESS_LOGGED_OUT => myvox_echo('LOGGED_OUT'),
	ACCESS_PRIVATE => myvox_echo('menu_builder:add:access:admin_only'),
];
$leftRight_options = [
    "left" => myvox_echo("left"),
    "right" => myvox_echo("right")
];
$parent_options = myvox_extract('parent_options', $vars);

$href = myvox_extract('href', $menu_item);
if (strpos($href, myvox_get_site_url()) === 0) {
	$href = substr($href, strlen(myvox_get_site_url()));
}

$default_access_id = ACCESS_PUBLIC;
if (myvox_get_config('walled_garden')) {
	$default_access_id = ACCESS_LOGGED_IN;
}

$form_body = '';

$form_body .= myvox_view('input/hidden', ['name' => 'menu_name', 'value' => myvox_extract('menu_name', $vars)]);
$form_body .= myvox_view('input/hidden', ['name' => 'name', 'value' => myvox_extract('name', $menu_item)]);
$form_body .= myvox_view('input/hidden', ['name' => 'priority', 'value' => myvox_extract('priority', $menu_item, time())]);

$form_body .= '<table class="mbm"><tr><td>';
$form_body .= myvox_format_element('label', [], myvox_echo('title'));
$form_body .= '</td><td>';

$form_body .= myvox_view('input/text', [
    'name' => 'text',
    'value' => myvox_extract('text', $menu_item),
]);

$label_info_text = myvox_echo('menu_builder:add:form:languagekey:info');
$label_info_text .= !empty($menu_item['languagekey']) ?
    myvox_echo('menu_builder:add:form:languagekey:info:more', array(
        $menu_name,
        $menu_item['languagekey']
    )) : "";
$label_title = myvox_format_element('label', [], myvox_echo('menu_builder:add:form:languagekey'));
$label_title .= myvox_view('output/pm_hint', [
    'id' => 'more_info_menu_languagekey',
    'text' => $label_info_text,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= $label_title;
$form_body .= '</td><td>';

$form_body .= myvox_view('input/text', [
    'name' => 'languagekey',
    'value' => myvox_extract('languagekey', $menu_item),
]);


$form_body .= '</td></tr><tr><td>';
$form_body .= myvox_format_element('label', [], myvox_echo('menu_builder:add:form:url'));
$form_body .= '</td><td>';

$form_body .= myvox_view('input/text', [
	'name' => 'href',
	'value' => $href,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= myvox_format_element('label', [], myvox_echo('menu_builder:add:form:access'));
$form_body .= '</td><td>';

$form_body .= myvox_view('input/access', [
	'name' => 'access_id',
	'value' => myvox_extract('access_id', $menu_item, $default_access_id),
	'options_values' => $access_options,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= myvox_format_element('label', [], myvox_echo('menu_builder:add:form:target'));
$form_body .= '</td><td>';

$form_body .= myvox_view('input/select', [
	'name' => 'target',
	'value' => myvox_extract('target', $menu_item, 0),
	'options_values' => $target_options,
]);

if ($parent_options) {
	array_unshift($parent_options, '');
	$form_body .= '</td></tr><tr><td>';
	$form_body .= myvox_format_element('label', [], myvox_echo('menu_builder:add:form:parent'));
	$form_body .= '</td><td>';

	$form_body .= myvox_view('input/select', [
		'name' => 'parent_name',
		'value' => myvox_extract('parent_name', $menu_item),
		'options_values' => $parent_options,
	]);
}

$form_body .= '</td></tr><tr><td>';
$form_body .= myvox_format_element('label', [], myvox_echo('menu_builder:add:form:float'));
$form_body .= '</td><td>';

$form_body .= myvox_view('input/access', [
    'name' => 'float',
    'value' => myvox_extract('float', $menu_item, 'left'),
    'options_values' => $leftRight_options,
]);

$form_body .= '</td></tr><tr><td>';
$form_body .= myvox_format_element('label', [], myvox_echo('menu_builder:add:action:tokens'));
$form_body .= '</td><td>';

$form_body .= myvox_view('input/checkbox', [
	'name' => 'is_action',
	'value' => 1,
	'checked' => (bool) myvox_extract('is_action', $menu_item),
]);

$form_body .= '</td></tr></table>';

$form_body .= myvox_view('input/submit', ['value' => myvox_echo('save')]);

echo $form_body;