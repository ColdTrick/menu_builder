<?php

echo elgg_format_element('span', ['class' => 'elgg-admin-notices'], elgg_format_element('p', [], elgg_echo('menu_builder:import:warning:title')));
echo elgg_echo('menu_builder:import:warning') . '<br><br>';

echo elgg_view_form('menu_builder/import', ['enctype' => 'multipart/form-data']);
