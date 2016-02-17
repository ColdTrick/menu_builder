<?php

$menu_name = get_input('menu_name');

$title = elgg_echo('menu_builder:import:title', [$menu_name]);

$body = elgg_format_element('span', ['class' => 'elgg-admin-notices'], elgg_format_element('p', [], elgg_echo('menu_builder:import:warning')));

$body .= elgg_view_form('menu_builder/menu/import', ['enctype' => 'multipart/form-data'], ['menu_name' => $menu_name]);

echo elgg_view_module('inline', $title, $body);
