<?php

// on activate generate the default site menu if that not has been done before
if (elgg_get_plugin_setting('menu_builder_default_imported', 'menu_builder', false)) {
	return true;
}

// register a hook to import the menu
elgg_register_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareSiteMenu', 900);

// trigger the menu so the hook will import it
elgg_view_menu('site');
