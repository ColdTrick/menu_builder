<?php

$menu_name = get_input('menu_name');

$title = myvox_echo('menu_builder:import:title', [$menu_name]);

$body = myvox_format_element('span', ['class' => 'myvox-admin-notices'], myvox_format_element('p', [], myvox_echo('menu_builder:import:warning')));

$body .= myvox_view_form('menu_builder/menu/import', ['enctype' => 'multipart/form-data'], ['menu_name' => $menu_name]);

echo myvox_view_module('inline', $title, $body);
