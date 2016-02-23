<?php

elgg_unset_plugin_setting('menu_builder_default_imported', 'menu_builder');

elgg_push_context('admin');

// register a hook to import the menu
elgg_register_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareSiteMenu', 900);

// triger pagesetup
elgg_view('output/text', ['value' => '']);

// restore unregistered function for the more menu
elgg_register_plugin_hook_handler('prepare', 'menu:site', '_elgg_site_menu_setup');

// unregister existing menu hooks
elgg_unregister_plugin_hook_handler('register', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::registerAllMenu');
elgg_unregister_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareAllMenu');

// trigger the menu so the hook will import it
elgg_view_menu('site');

elgg_pop_context();
