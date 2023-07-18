<?php

$menu_item = elgg_extract('menu_item', $vars, []);

$target_options = [
	'0' => elgg_echo('menu_builder:add:form:target:self'),
	'_blank' => elgg_echo('menu_builder:add:form:target:blank'),
];
$access_options = [
	ACCESS_PUBLIC => elgg_echo('access:label:public'),
	ACCESS_LOGGED_IN => elgg_echo('access:label:logged_in'),
	\ColdTrick\MenuBuilder\Menu::ACCESS_LOGGED_OUT => elgg_echo('access:label:logged_out'),
	ACCESS_PRIVATE => elgg_echo('access:admin_only'),
];

$parent_options = elgg_extract('parent_options', $vars);

$href = (string) elgg_extract('href', $menu_item);
if (strpos($href, elgg_get_site_url()) === 0) {
	$href = substr($href, strlen(elgg_get_site_url()));
}

$default_access_id = ACCESS_PUBLIC;
if (elgg_get_config('walled_garden')) {
	$default_access_id = ACCESS_LOGGED_IN;
}

$fields = [
	[
		'#type' => 'hidden',
		'name' => 'menu_name',
		'value' => elgg_extract('menu_name', $vars),
	],
	[
		'#type' => 'hidden',
		'name' => 'name',
		'value' => elgg_extract('name', $menu_item),
	],
	[
		'#type' => 'hidden',
		'name' => 'priority',
		'value' => elgg_extract('priority', $menu_item, time()),
	],
	[
		'#type' => 'text',
		'#label' => elgg_echo('title'),
		'name' => 'text',
		'value' => elgg_extract('text', $menu_item),
	],
	[
		'#type' => 'text',
		'#label' => elgg_echo('menu_builder:add:form:url'),
		'name' => 'href',
		'value' => $href,
	],
	[
		'#type' => 'text',
		'#label' => elgg_echo('menu_builder:add:form:icon'),
		'name' => 'icon',
		'value' => elgg_extract('icon', $menu_item),
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('menu_builder:add:action:tokens'),
		'name' => 'is_action',
		'value' => 1,
		'checked' => (bool) elgg_extract('is_action', $menu_item),
		'switch' => true,
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('menu_builder:add:lightbox'),
		'name' => 'lightbox',
		'value' => 1,
		'checked' => (bool) elgg_extract('lightbox', $menu_item),
		'switch' => true,
	],
	[
		'#type' => 'access',
		'#label' => elgg_echo('menu_builder:add:form:access'),
		'name' => 'access_id',
		'value' => elgg_extract('access_id', $menu_item, $default_access_id),
		'options_values' => $access_options,
	],
	[
		'#type' => 'select',
		'#label' => elgg_echo('menu_builder:add:form:target'),
		'name' => 'target',
		'value' => elgg_extract('target', $menu_item, 0),
		'options_values' => $target_options,
	],
];

if ($parent_options) {
	array_unshift($parent_options, '');
	
	$fields[] = [
		'#type' => 'select',
		'#label' => elgg_echo('menu_builder:add:form:parent'),
		'name' => 'parent_name',
		'value' => elgg_extract('parent_name', $menu_item),
		'options_values' => $parent_options,
	];
}

echo elgg_view('input/fieldset', ['fields' => $fields, 'class' => 'mbm']);

$footer = elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);
