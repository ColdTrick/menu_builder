<?php

myvox_unset_plugin_setting('menu_builder_default_imported', 'menu_builder');

myvox_push_context('admin');

// register a hook to import the menu
myvox_register_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareSiteMenu', 900);

// restore unregistered function for the more menu
myvox_register_plugin_hook_handler('prepare', 'menu:site', '_myvox_site_menu_setup');

// unregister existing menu hooks
myvox_unregister_plugin_hook_handler('register', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::registerAllMenu');
myvox_unregister_plugin_hook_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\MenuHooks::prepareAllMenu');

// trigger the menu so the hook will import it
myvox_view_menu('site');

myvox_pop_context();
