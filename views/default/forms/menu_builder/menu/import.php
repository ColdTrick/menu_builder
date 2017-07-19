<?php

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'menu_name',
	'value' => elgg_extract('menu_name', $vars),
]);

echo elgg_view_field([
	'#type' => 'file',
	'#help' => elgg_echo('menu_builder:import:help'),
	'name' => 'import',
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('import'),
]);

elgg_set_form_footer($footer);
