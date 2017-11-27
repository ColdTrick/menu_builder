<?php
$menu_name = elgg_extract('name', $vars);
$data = false;
if (!elgg_in_context('admin')) {
	$menu = new \ColdTrick\MenuBuilder\Menu($menu_name);
	$data = $menu->getCachedData();
}

if ($data) {
	echo $data;
} elseif ($menu_name !== 'site') {
	echo elgg_view('navigation/menu/default', $vars);
}

if (!$data) {
	// hook after view to save cache
	elgg_register_plugin_hook_handler('view', "navigation/menu/{$menu_name}", '\ColdTrick\MenuBuilder\MenuHooks::viewMenu', 999);
}