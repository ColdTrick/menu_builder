<?php

$menu_name = get_input('menu_name');

$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
$menu->delete();
