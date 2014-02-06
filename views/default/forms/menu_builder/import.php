<?php

echo elgg_view('input/file', array('name' => 'import'));
echo elgg_view('output/longtext', array(
	'value' => elgg_echo('menu_builder:import:help'),
	'class' => 'elgg-subtext'
));

echo elgg_view('input/submit', array('value' => elgg_echo('admin:menu_builder:import')));
