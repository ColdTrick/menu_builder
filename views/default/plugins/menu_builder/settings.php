<?php

$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('menu_builder:settings:htmlawed:filter'),
	'name' => 'params[htmlawed_filter]',
	'checked' => $plugin->htmlawed_filter !== 'no',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

$setting = elgg_echo('menu_builder:settings:regen_site_menu') . ' ';
$setting .= elgg_view('output/url', [
	'text' => elgg_echo('menu_builder:settings:regen_site_menu:button'),
	'href' => elgg_generate_action_url('menu_builder/regen_site_menu'),
	'confirm' => true,
]);

echo elgg_view_field(['#html' => $setting]);
