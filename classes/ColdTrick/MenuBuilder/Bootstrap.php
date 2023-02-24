<?php

namespace ColdTrick\MenuBuilder;

use Elgg\DefaultPluginBootstrap;

/**
 * Plugin bootstrap
 */
class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function ready() {
		$events = $this->elgg()->events;
		
		$managed_menus = menu_builder_get_managed_menus();
		if (in_array('site', $managed_menus)) {
			// take control of menu setup
			$events->unregisterHandler('prepare', 'menu:site', 'Elgg\Menus\Site::reorderItems');
		}
		
		foreach ($managed_menus as $menu_name) {
			$events->registerHandler('register', "menu:{$menu_name}", __NAMESPACE__ . '\Menus::registerAllMenu', 999);
			$events->registerHandler('prepare', "menu:{$menu_name}", __NAMESPACE__ . '\Menus::prepareAllMenu', 999);
		}
	}
}
