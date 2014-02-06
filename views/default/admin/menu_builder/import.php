<?php

echo elgg_view('admin/menu_builder/navigation');

echo "<span class='elgg-admin-notices'><p>" . elgg_echo('menu_builder:import:warning:title') . "</p></span>";
echo elgg_echo('menu_builder:import:warning') . '<br><br>';

echo elgg_view_form('menu_builder/import', array('enctype' => 'multipart/form-data'));
