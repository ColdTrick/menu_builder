<?php

namespace ColdTrick\MenuBuilder;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function ready() {
		$hooks = $this->elgg()->hooks;
		
		$managed_menus = menu_builder_get_managed_menus();
		if (in_array('site', $managed_menus)) {
			// take control of menu setup
			$hooks->unregisterHandler('prepare', 'menu:site', 'Elgg\Menus\Site::reorderItems');
		}
		
		foreach ($managed_menus as $menu_name) {
			$hooks->registerHandler('register', "menu:{$menu_name}", __NAMESPACE__ . '\MenuHooks::registerAllMenu', 999);
			$hooks->registerHandler('prepare', "menu:{$menu_name}", __NAMESPACE__ . '\MenuHooks::prepareAllMenu', 999);
		}
	}
}
