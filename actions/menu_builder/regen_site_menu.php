<?php

$plugin = elgg_get_plugin_from_id('menu_builder');
$plugin->unsetSetting('menu_builder_default_imported');

elgg_push_context('admin');

// register a hook to import the menu
elgg_register_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareSiteMenu', 900);

// restore unregistered function for the more menu
elgg_register_plugin_hook_handler('prepare', 'menu:site', 'Elgg\Menus\Site::reorderItems');

// unregister existing menu hooks
elgg_unregister_plugin_hook_handler('register', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::registerAllMenu');
elgg_unregister_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareAllMenu');

// trigger the menu so the hook will import it
elgg_view_menu('site');

elgg_pop_context();
