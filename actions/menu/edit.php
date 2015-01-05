<?php

$menu_name = get_input("menu_name"); // @todo check for valid menu name

if ($menu_name) {
	menu_builder_add_menu($menu_name);
}
