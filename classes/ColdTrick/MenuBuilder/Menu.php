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

		if (isset($menu_item['href'])) {
			if (strpos($menu_item['href'], elgg_get_site_url()) === 0) {
				$menu_item['href'] = substr($menu_item['href'], strlen(elgg_get_site_url()));
			}
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
	public function setMenuConfig($config = []) {
		elgg_set_plugin_setting("menu_{$this->name}_config", json_encode($config), 'menu_builder');
		elgg_reset_system_cache();
	}
	
	/**
	 * Returns an array of items to be used in edit forms of menu items
	 *
	 * @param string $skip_menu_item skip this menu item
	 *
	 * @return array
	 */
	public function getInputOptions($skip_menu_item) {
		$menu = elgg_trigger_plugin_hook('register', "menu:{$this->name}", ['name' => $this->name], []);
		$builder = new \ElggMenuBuilder($menu);
		$menu = $builder->getMenu('priority');
		
		$menu = elgg_extract('default', $menu);
		return $this->getIndentedOptions($menu, $skip_menu_item);
	}
	
	/**
	 * Returns an array of indented menu items
	 *
	 * @param array  $menu_items     array of menu items
	 * @param string $skip_menu_item skip this menu item
	 * @param int    $indent         number of indents
	 *
	 * @return array
	 */
	private function getIndentedOptions($menu_items, $skip_menu_item, $indent = 0) {
		$result = [];
		
		foreach ($menu_items as $menu_item) {
			if ($menu_item->getName() == $skip_menu_item) {
				continue;
			}
			$text = str_repeat('-', $indent) . ' ' . $menu_item->getText();
			$result[$menu_item->getName()] = $text;
			
			$children = $menu_item->getChildren();
			if (empty($children)) {
				continue;
			}
			
			$children_options = $this->getIndentedOptions($children, $skip_menu_item, $indent + 1);
			if (!empty($children_options)) {
				$result = $result + $children_options;
			}
		}
		
		return $result;
	}
	
	/**
	 * Returns the name of the cachefile to be used
	 *
	 * @return string
	 */
	private function getCacheName() {
		$cache_name = "{$this->name}_logged_in";
		if (!elgg_is_logged_in()) {
			$cache_name = "{$this->name}_logged_out";
		} elseif (elgg_is_admin_logged_in()) {
			$cache_name = "{$this->name}_admin";
		}
	
		return $cache_name;
	}
}