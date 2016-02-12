<?php

$plugin = elgg_extract('entity', $vars);

$setting = elgg_echo('menu_builder:htmlawed:filter') . '<br>';

$setting .= elgg_view('input/dropdown', [
	'name' => 'params[htmlawed_filter]',
	'value' => $plugin->htmlawed_filter ? $plugin->htmlawed_filter : 'yes',
	'options_values' => [
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no'),
	],
]);

echo elgg_format_element('div', [], $setting);
