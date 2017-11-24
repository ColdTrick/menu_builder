<?php

echo myvox_view('input/hidden', [
	'name' => 'menu_name',
	'value' => myvox_extract('menu_name', $vars),
]);
echo myvox_view('input/file', ['name' => 'import']);
echo myvox_view('output/longtext', [
	'value' => myvox_echo('menu_builder:import:help'),
	'class' => 'myvox-subtext',
]);

echo myvox_view('input/submit', ['value' => myvox_echo('import')]);
