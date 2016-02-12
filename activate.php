<?php

require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/hooks.php');

// on activate generate the default site menu if that not has been done before
if (elgg_get_plugin_setting('menu_builder_default_imported', 'menu_builder', false)) {
	return;
}

// register a hook to import the menu
elgg_register_plugin_hook_handler('prepare', 'menu:site', 'menu_builder_site_menu_prepare', 900);

// trigger the menu so the hook will import it
elgg_view_menu('site');
