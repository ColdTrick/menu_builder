<?php

namespace ColdTrick\MenuBuilder;

use Elgg\Menu\MenuItems;

/**
 * Menu
 */
class Menu {
	
	const ACCESS_LOGGED_OUT = -5;
	
	protected string $name;
	
	/**
	 * Constructor
	 *
	 * @param string $menu_name name of the menu
	 */
	public function __construct(string $menu_name) {
		$this->name = $menu_name;
	}
	
	/**
	 * Adds the menu name to the list of manageable menus
	 *
	 * @return void
	 */
	public function save(): void {
		$menus = menu_builder_get_managed_menus();
		if (in_array($this->name, $menus)) {
			// already exists
			return;
		}
		
		$menus[] = $this->name;
		
		elgg_get_plugin_from_id('menu_builder')->menu_names = json_encode($menus);
	}
	
	/**
	 * Removes the menus
	 *
	 * @return void
	 */
	public function delete(): void {
		$menus = menu_builder_get_managed_menus();
		if (!in_array($this->name, $menus)) {
			return;
		}
		
		$key = array_search($this->name, $menus);
		unset($menus[$key]);
		
		$plugin = elgg_get_plugin_from_id('menu_builder');
		$plugin->menu_names = json_encode($menus);
		$plugin->unsetSetting("menu_{$this->name}_config");
	}
	
	/**
	 * Adds a new menu item
	 *
	 * @param array $params parameters of the menu item
	 *
	 * @return void
	 */
	public function addMenuItem(array $params = []): void {
		if (empty($params)) {
			return;
		}
		
		$defaults = [
			'access_id' => ACCESS_PUBLIC,
			'is_action' => false,
			'lightbox' => false,
			'priority' => time(),
		];
		
		$menu_item = array_merge($defaults, $params);
		
		$current_config = $this->getMenuConfig();
		
		$name = elgg_extract('name', $menu_item);
		if (empty($name)) {
			$time = time();
			$name = "menu_name_{$time}";
			while (array_key_exists($name, $current_config)) {
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
	 * Removes a menu item
	 *
	 * @param string $name name of the menu item to remove
	 *
	 * @return void
	 */
	public function removeMenuItem(string $name): void {
		$current_config = $this->getMenuConfig();
		unset($current_config[$name]);
		$this->setMenuConfig($current_config);
		
		foreach ($current_config as $key => $item) {
			if (elgg_extract('parent_name', $item) === $name) {
				$this->removeMenuItem($key);
			}
		}
	}
	
	/**
	 * Returns the current menu items config
	 *
	 * @return array
	 */
	public function getMenuConfig(): array {
		$setting = elgg_get_plugin_setting("menu_{$this->name}_config", 'menu_builder');
		if (empty($setting)) {
			return [];
		}
		
		$config = json_decode($setting, true);
		return is_array($config) ? $config : [];
	}
	
	/**
	 * Saves the menu configuration
	 *
	 * @param array $config configuration of menu items
	 *
	 * @return void
	 */
	public function setMenuConfig(array $config = []): void {
		elgg_get_plugin_from_id('menu_builder')->{"menu_{$this->name}_config"} = json_encode($config);
	}
	
	/**
	 * Returns an array of items to be used in edit forms of menu items
	 *
	 * @param string $skip_menu_item skip this menu item
	 *
	 * @return array
	 */
	public function getInputOptions(string $skip_menu_item = ''): array {
		$menu = elgg_trigger_event_results('register', "menu:{$this->name}", ['name' => $this->name], new MenuItems());
		$builder = new \ElggMenuBuilder($menu);
		$menu = $builder->getMenu('priority');
		
		$items = [];
		$default = elgg_extract('default', $menu);
		if ($default instanceof \Elgg\Menu\MenuSection) {
			$items = $default->getItems();
		}
		
		return $this->getIndentedOptions($items, $skip_menu_item);
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
	protected function getIndentedOptions(array $menu_items, string $skip_menu_item = '', int $indent = 0): array {
		if (empty($menu_items)) {
			return [];
		}
		
		$result = [];
		foreach ($menu_items as $menu_item) {
			if ($menu_item->getName() === $skip_menu_item) {
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
}
