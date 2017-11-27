<?php

$plugin = elgg_extract('entity', $vars);

$setting = elgg_echo('menu_builder:settings:htmlawed:filter');

$setting .= elgg_view('input/dropdown', [
	'name' => 'params[htmlawed_filter]',
	'value' => $plugin->htmlawed_filter ? $plugin->htmlawed_filter : 'yes',
	'options_values' => [
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no'),
	],
	'class' => 'mls',
]);

echo elgg_format_element('div', [], $setting);

$setting = elgg_echo('menu_builder:settings:regen_site_menu') . ' ';
$setting .= elgg_view('output/url', [
	'text' => elgg_echo('menu_builder:settings:regen_site_menu:button'),
	'href' => 'action/menu_builder/regen_site_menu',
	'confirm' => elgg_echo('question:areyousure'),
]);


echo elgg_format_element('div', [], $setting);
