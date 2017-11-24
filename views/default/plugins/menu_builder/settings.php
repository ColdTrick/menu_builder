<?php

$plugin = myvox_extract('entity', $vars);

$setting = myvox_echo('menu_builder:settings:htmlawed:filter');

$setting .= myvox_view('input/dropdown', [
	'name' => 'params[htmlawed_filter]',
	'value' => $plugin->htmlawed_filter ? $plugin->htmlawed_filter : 'yes',
	'options_values' => [
		'yes' => myvox_echo('option:yes'),
		'no' => myvox_echo('option:no'),
	],
	'class' => 'mls',
]);

echo myvox_format_element('div', [], $setting);

$setting = myvox_echo('menu_builder:settings:regen_site_menu') . ' ';
$setting .= myvox_view('output/url', [
	'text' => myvox_echo('menu_builder:settings:regen_site_menu:button'),
	'href' => 'action/menu_builder/regen_site_menu',
	'confirm' => myvox_echo('question:areyousure'),
]);


echo myvox_format_element('div', [], $setting);
