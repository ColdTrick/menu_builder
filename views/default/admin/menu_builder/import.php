<?php

echo elgg_view('admin/menu_builder/navigation');

echo '<br><br>';
echo "<h4>" . elgg_echo('menu_builder:import:warning:title') . "</h4>";
echo elgg_echo('menu_builder:import:warning') . '<br><br>';

echo elgg_view_form('menu_builder/import', array('enctype' => 'multipart/form-data'));
