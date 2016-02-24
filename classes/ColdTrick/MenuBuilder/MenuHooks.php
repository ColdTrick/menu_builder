<?php

namespace ColdTrick\MenuBuilder;

class MenuHooks {
	/**
	 * Adds the menu items to the menus managed by menu_builder
	 *
	 * @param string  $hook   name of the hook
	 * @param string  $type   type of the hook
	 * @param unknown $return return value
	 * @param unknown $params hook parameters
	 *
	 * @return array
	 */
	public static function registerAllMenu($hook, $type, $return, $params) {
		$current_menu = $params['name'];
		$return = []; // need to reset as there should be no other way to add menu items
	
		$menu = new \ColdTrick\MenuBuilder\Menu($current_menu);
		
		if (!elgg_in_context('admin') && $menu->getCachedData()) {
			// don't get menu as it will be handle by the cache @see menu_builder_view_navigation_menu_default_hook
			return $return;
		}
	
		// fix menu name if needed
		$lang_key = 'menu:' . elgg_get_friendly_title($current_menu) . ':header:default';
		if (!elgg_language_key_exists($lang_key)) {
			add_translation(get_current_language(), [$lang_key => $current_menu]);
		}
	
		// add configured menu items
		$menu_items = json_decode(elgg_get_plugin_setting("menu_{$current_menu}_config", 'menu_builder'), true);
	
		if (is_array($menu_items)) {
			foreach ($menu_items as $menu_item) {
				$can_add_menu_item = true;
					
				if (elgg_in_context('menu_builder_manage')) {
					$menu_item['menu_builder_menu_name'] = $current_menu;
				} else {
						
					if (empty($menu_item['target'])) {
						unset($menu_item['target']);
					}
	
					$access_id = $menu_item['access_id'];
					unset($menu_item['access_id']);
					switch($access_id) {
						case ACCESS_PRIVATE:
							if (!elgg_is_admin_logged_in()) {
								$can_add_menu_item = false;
							}
							break;
						case MENU_BUILDER_ACCESS_LOGGED_OUT:
							if (elgg_is_logged_in()) {
								$can_add_menu_item = false;
							}
							break;
						case ACCESS_LOGGED_IN:
							if (!elgg_is_logged_in()) {
								$can_add_menu_item = false;
							}
							break;
					}
				}
					
				if (!$can_add_menu_item) {
					continue;
				}
					
				// strip out deprecated use of [wwwroot] as menu items will be normalized by default
				$menu_item['href'] = str_replace('[wwwroot]', '', $menu_item['href']);
					
				// add global replacable action tokens
				if ($menu_item['is_action'] && !elgg_in_context('menu_builder_manage')) {
					unset($menu_item['is_action']);
	
					$concat = '?';
					if (stristr($menu_item['href'], '?')) {
						$concat = '&';
					}
					$menu_item['href'] .= $concat . '__elgg_ts=[__elgg_ts]&__elgg_token[__elgg_token]';
				}
					
				if (empty($menu_item['href'])) {
					$menu_item['href'] = false;
				}
				$return[] = \ElggMenuItem::factory($menu_item);
			}
		}
	
		// add 'new menu item' menu item
		if (elgg_in_context('menu_builder_manage')) {
			$return[] = \ElggMenuItem::factory([
				'name' => 'menu_builder_add',
				'text' => '<strong>+</strong>&nbsp;&nbsp;' . elgg_echo('menu_builder:edit_mode:add'),
				'href' => 'ajax/view/menu_builder/edit_item?item_name=menu_builder_add&menu_name=' . $current_menu,
				'link_class' => 'elgg-lightbox',
				'menu_builder_menu_name' => $current_menu,
				'priority' => time(),
			]);
		}
	
		return $return;
	}

	/**
	 * Makes menus managable if needed
	 *
	 * @param string  $hook   name of the hook
	 * @param string  $type   type of the hook
	 * @param unknown $return return value
	 * @param unknown $params hook parameters
	 *
	 * @return array
	 */
	public static function prepareAllMenu($hook, $type, $return, $params) {
	
		// update order
		$ordered = [];
	
		if (isset($return['default'])) {
			foreach ($return['default'] as $menu_item) {
	
				$menu_item = self::orderMenuItem($menu_item, 2);
				$priority = $menu_item->getPriority();
				while (array_key_exists($priority, $ordered)) {
					$priority++;
				}
				$ordered[$priority] = $menu_item;
			}
		}
	
		ksort($ordered);
	
		$return['default'] = $ordered;
	
		// prepare menu items for edit
		if (elgg_in_context('menu_builder_manage')) {
	
			$menu = elgg_extract('default', $return);
	
			self::prepareMenuItemsEdit($menu);
		}
	
		return $return;
	}
	
	/**
	 * Caches menus
	 *
	 * @param string  $hook   name of the hook
	 * @param string  $type   type of the hook
	 * @param unknown $return return value
	 * @param unknown $params hook parameters
	 *
	 * @return void
	 */
	public static function viewMenu($hook, $type, $return, $params) {
		if (elgg_in_context('admin')) {
			return;
		}
	
		$menu = new \ColdTrick\MenuBuilder\Menu($params['vars']['name']);
		$menu->saveToCache($return);
	}
	
	/**
	 * Replaces dynamic data in menu's
	 *
	 * @param string  $hook   name of the hook
	 * @param string  $type   type of the hook
	 * @param unknown $return return value
	 * @param unknown $params hook parameters
	 *
	 * @return void
	 */
	public static function afterViewMenu($hook, $type, $return, $params) {
		if (empty($return)) {
			return $return;
		}
	
		// fill in username/userguid
		$user = elgg_get_logged_in_user_entity();
		if ($user) {
			$return = str_replace('[username]', $user->username, $return);
			$return = str_replace('[userguid]', $user->guid, $return);
		} else {
			$return = str_replace('[username]', '', $return);
			$return = str_replace('[userguid]', '', $return);
		}
	
		// add in tokens
		$elgg_ts = time();
		$elgg_token = generate_action_token($elgg_ts);
	
		$return = str_replace('[__elgg_ts]', $elgg_ts, $return);
		$return = str_replace('[__elgg_token]', $elgg_token, $return);
	
		return $return;
	}
	
	/**
	 * Make sure all items are selected correctly
	 *
	 * @param string  $hook   name of the hook
	 * @param string  $type   type of the hook
	 * @param unknown $return return value
	 * @param unknown $params hook parameters
	 *
	 * @return array
	 */
	public static function prepareMenuSetSelected($hook, $type, $return, $params) {
	
		if (strpos($type, 'menu:') !== 0) {
			return $return;
		}
	
		if (empty($params) || !is_array($params)) {
			return $return;
		}
	
		// set selected state on parent menu items
		$item = elgg_extract('selected_item', $params);
		if (empty($item) || !($item instanceof \ElggMenuItem)) {
			return $return;
		}
	
		while ($item && ($item = $item->getParent())) {
			$item->setSelected(true);
		}
	}
	
	/**
	 * Loads initially the site menu into the menu_builder config.
	 *
	 * @param string  $hook   name of the hook
	 * @param string  $type   type of the hook
	 * @param unknown $return return value
	 * @param unknown $params hook parameters
	 *
	 * @return array
	 */
	public static function prepareSiteMenu($hook, $type, $return, $params) {
		if (elgg_get_plugin_setting('menu_builder_default_imported', 'menu_builder', false)) {
			return;
		}
	
		$menu = new \ColdTrick\MenuBuilder\Menu('site');
		$menu->save();
		
		// remove potential existing menu items
		elgg_unset_plugin_setting('menu_site_config', 'menu_builder');
		
		$priority = 10;

		foreach ($return as $section => $items) {
			$parent_name = null;
	
			if ($section !== 'default') {
				$menu->addMenuItem([
					'name' => $section,
					'text' => elgg_echo($section),
					'href' => '#',
					'priority' => $priority,
				]);
	
				$parent_name = $section;
				$priority += 10;
			}
	
			foreach ($items as $item) {
				$menu->addMenuItem([
					'name' => $item->getName(),
					'text' => $item->getText(),
					'href' => str_replace(elgg_get_site_url(), '', $item->getHref()),
					'priority' => $priority,
					'parent_name' => $parent_name,
				]);
	
				$priority += 10;
			}
		}
	
		elgg_set_plugin_setting('menu_builder_default_imported', time(), 'menu_builder');
	}
	
	/**
	 * Prepares menu items to be edited
	 *
	 * @param array $menu array of \ElggMenuItem objects
	 *
	 * @return void
	 */
	private static function prepareMenuItemsEdit($menu) {
		foreach ($menu as $menu_item) {
			$text = $menu_item->getText();
	
			$name = $menu_item->getName();
			$menu_name = $menu_item->menu_builder_menu_name;
			
			if ($name == 'menu_builder_add') {
				continue;
			}
			
			$text .= elgg_format_element('span', [
				'title' => elgg_echo('edit'),
				'class' => 'elgg-lightbox',
				'data-colorbox-opts' => json_encode(['href' => elgg_normalize_url("ajax/view/menu_builder/edit_item?item_name={$name}&menu_name={$menu_name}")]),
			], elgg_view_icon('settings-alt'));
			
			$text .= elgg_format_element('span', [
				'title' => elgg_echo('delete'),
				'data-href' => elgg_add_action_tokens_to_url("action/menu_builder/menu_item/delete?item_name={$name}&menu_name={$menu_name}"),
			], elgg_view_icon('delete'));

			$text = elgg_view('output/url', ['href' => '#', 'text' => $text]);

			$menu_item->setText($text);
			$menu_item->setHref(false);
	
			$children = $menu_item->getChildren();
			if ($children) {
				self::prepareMenuItemsEdit($children);
			}
			
			// add a placeholder child menu item for sorting
			$menu_item->addChild(\ElggMenuItem::factory([
				'name' => 'placeholder',
				'text' => elgg_echo('menu_builder:admin:menu:placeholder'),
				'href' => '#',
				'item_class' => 'hidden',
			]));
		}
	}
	
	/**
	 * Reorders menu item and adds an add button
	 *
	 * @param \ElggMenuItem $item  menu item
	 * @param int           $depth depth of the menu item
	 *
	 * @return \ElggMenuItem
	 */
	private static function orderMenuItem(\ElggMenuItem $item, $depth) {
	
		$depth = (int) $depth;
		$children = $item->getChildren();
		if (empty($children)) {
			return $item;
		}
	
		// sort children
		$ordered_children = [];
	
		foreach ($children as $child) {
	
			$child = self::orderMenuItem($child, $depth + 1);
	
			$child_priority = $child->getPriority();
			while (array_key_exists($child_priority, $ordered_children)) {
				$child_priority++;
			}
			$ordered_children[$child_priority] = $child;
		}
		ksort($ordered_children);
	
		$item->setChildren($ordered_children);
	
		return $item;
	}
}