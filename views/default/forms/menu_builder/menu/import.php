<?php

echo elgg_view('input/hidden', [
	'name' => 'menu_name',
	'value' => elgg_extract('menu_name', $vars),
]);
echo elgg_view('input/file', ['name' => 'import']);
echo elgg_view('output/longtext', [
	'value' => elgg_echo('menu_builder:import:help'),
	'class' => 'elgg-subtext',
]);

echo elgg_view('input/submit', ['value' => elgg_echo('import')]);
