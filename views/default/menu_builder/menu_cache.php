<?php
$menu_name = $vars["name"];
$data = menu_builder_get_menu_cache($menu_name);

if ($data) {
	echo $data;
} elseif ($menu_name !== "site") {
	echo elgg_view("navigation/menu/default", $vars);
}
