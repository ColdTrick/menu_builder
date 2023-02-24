<?php

$plugin = elgg_get_plugin_from_id('menu_builder');
$plugin->unsetSetting('menu_builder_default_imported');

elgg_push_context('admin');

// register a event to import the menu
elgg_register_event_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\Menus::prepareSiteMenu', 900);

// restore unregistered function for the more menu
elgg_register_event_handler('prepare', 'menu:site', 'Elgg\Menus\Site::reorderItems');

// unregister existing menu events
elgg_unregister_event_handler('register', 'menu:site', '\ColdTrick\MenuBuilder\Menus::registerAllMenu');
elgg_unregister_event_handler('prepare', 'menu:site', '\ColdTrick\MenuBuilder\Menus::prepareAllMenu');

// trigger the menu so the event will import it
elgg_view_menu('site');

elgg_pop_context();
