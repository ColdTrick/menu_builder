<?php

echo "<div>";
echo elgg_view('admin/menu_builder/navigation');

echo elgg_echo('menu_builder:htmlawed:filter') . '<br>';

echo elgg_view('input/dropdown', array(
	'name' => 'params[htmlawed_filter]',
	'value' => $vars['entity']->htmlawed_filter ? $vars['entity']->htmlawed_filter : 'yes',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));

echo "</div>";
