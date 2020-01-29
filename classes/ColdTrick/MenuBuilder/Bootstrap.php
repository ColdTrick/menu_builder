<?php

namespace ColdTrick\MenuBuilder;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		// ajax views
		elgg_register_ajax_view('menu_builder/import');
		elgg_register_ajax_view('menu_builder/edit_item');
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function ready() {
		$hooks = $this->elgg()->hooks;
		
		if (menu_builder_is_managed_menu('site')) {
			// take control of menu setup
			$hooks->unregisterHandler('prepare', 'menu:site', '_elgg_site_menu_setup');
		}
		
		$managed_menus = menu_builder_get_managed_menus();
		foreach ($managed_menus as $menu_name) {
			$hooks->registerHandler('register', "menu:{$menu_name}", __NAMESPACE__ . '\MenuHooks::registerAllMenu', 999);
			$hooks->registerHandler('prepare', "menu:{$menu_name}", __NAMESPACE__ . '\MenuHooks::prepareAllMenu', 999);
		}
	}
}
