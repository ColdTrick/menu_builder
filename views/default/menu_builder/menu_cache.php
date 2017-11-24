<?php
$menu_name = myvox_extract('name', $vars);
$data = false;
if (!myvox_in_context('admin')) {
	$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
	$data = $menu->getCachedData();
}

if ($data) {
	echo $data;
} elseif ($menu_name !== 'site') {
	echo myvox_view('navigation/menu/default', $vars);
}

if (!$data) {
	// hook after view to save cache
	myvox_register_plugin_hook_handler('view', "navigation/menu/{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::viewMenu', 999);
}