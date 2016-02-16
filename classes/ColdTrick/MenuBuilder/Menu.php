<?php

namespace ColdTrick\MenuBuilder;

/**
 * Menu
 */
class Menu {
	
	private $name;
	
	public function __construct($menu_name) {
		$this->name = $menu_name;
	}
	
	/**
	 * Adds the menu name to the list of manageable menus
	 *
	 * @return void
	 */
	public function save() {
		$menus = menu_builder_get_managed_menus();
		if (!is_array($menus)) {
			$menus = [];
		}
		
		if (in_array($this->name, $menus)) {
			// already exists
			return;
		}
		
		$menus[] = $this->name;
		
		elgg_set_plugin_setting('menu_names', json_encode($menus), 'menu_builder');
		elgg_reset_system_cache();
	}
	
	/**
	 * Checks if cached menu html is available and returns the html if it is available
	 *
	 * @return boolean|string
	 */
	public function getCachedData() {
		if (!elgg_get_config('system_cache_enabled')) {
			return false;
		}
		
		return elgg_load_system_cache($this->getCacheName()) ?: false;
	}
	
	/**
	 * Saves data to cache location
	 *
	 * @param string $data data to be saved
	 *
	 * @return void
	 */
	public function saveToCache($data) {
		elgg_save_system_cache($this->getCacheName(), $data);
	}
	
	/**
	 * Adds a new menu item
	 *
	 * @param array $params parameters of the menu item
	 *
	 * @return void
	 */
	public function addMenuItem($params = []) {
		if (empty($params)) {
			return;
		}
		
		$defaults = [
			'access_id' => ACCESS_PUBLIC,
			'is_action' => false,
			'priority' => time(),
		];
		
		$menu_item = array_merge($defaults, $params);
		
		$current_config = $this->getMenuConfig();
		
		$name = elgg_extract('name', $menu_item);
		if (empty($name)) {
			$time = time();
			$name = "menu_name_{$time}";
			while (in_array($name, $current_config)) {
				$time++;
				$name = "menu_name_{$time}";
			}
		
			$menu_item['name'] = $name;
		}
		
		$current_config[$name] = $menu_item;
		
		$this->setMenuConfig($current_config);
	}
	
	/**
	 * Returns the current menu items config
	 *
	 * @return array
	 */
	public function getMenuConfig() {
		$config = json_decode(elgg_get_plugin_setting("menu_{$this->name}_config", 'menu_builder'), true);
		if (!is_array($config)) {
			$config = [];
		}
		
		return $config;
	}
	
	/**
	 * Saves the menu configuration
	 *
	 * @param array $config configuration of menu items
	 */
	private function setMenuConfig($config) {
		elgg_set_plugin_setting("menu_{$this->name}_config", json_encode($config), 'menu_builder');
		elgg_reset_system_cache();
	}
	
	/**
	 * Returns the name of the cachefile to be used
	 *
	 * @return string
	 */
	private function getCacheName() {
		$cache_name = "{$this->name}_logged_in";
		if (!elgg_is_logged_in()) {
			$cache_name = "{$this->_name}_logged_out";
		} elseif (elgg_is_admin_logged_in()) {
			$cache_name = "{$this->_name}_admin";
		}
	
		return $cache_name;
	}
}