<?php

$menu_name = get_input('menu_name');

$body = elgg_view_message('warning', elgg_echo('menu_builder:import:warning'));
$body .= elgg_view_form('menu_builder/menu/import', [], ['menu_name' => $menu_name]);

echo elgg_view_module('info', elgg_echo('menu_builder:import:title', [$menu_name]), $body);
