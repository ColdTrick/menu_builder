<?php

$menu_name = get_input("menu_name");

$managed_menus = menu_builder_get_managed_menus();

if (!in_array($menu_name, $managed_menus)) {
	register_error("Invalid menu name");
	forward(REFERER);
}

menu_builder_add_menu_item($menu_name);

system_message("menu item saved");
forward(REFERER);
