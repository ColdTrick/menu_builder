<?php

namespace ColdTrick\MenuBuilder;

use Elgg\Menu\MenuItems;

/**
 * Menu related callbacks
 */
class Menus {
	
	/**
	 * Adds the menu items to the menus managed by menu_builder
	 *
	 * @param \Elgg\Event $event 'register', "menu:{$menu_name}"
	 *
	 * @return MenuItems
	 */
	public static function registerAllMenu(\Elgg\Event $event) {
		$current_menu = $event->getParam('name');
		/* @var $return MenuItems */
		$return = $event->getValue();
		$return->fill([]); // need to reset as there should be no other way to add menu items
		
		$menu = new \ColdTrick\MenuBuilder\Menu($current_menu);
	
		// fix menu name if needed
		$lang_key = 'menu:' . elgg_get_friendly_title($current_menu) . ':header:default';
		if (!elgg_language_key_exists($lang_key)) {
			elgg()->translator->addTranslation(elgg_get_current_language(), [$lang_key => $current_menu]);
		}
	
		// add configured menu items
		$menu_items = $menu->getMenuConfig();
	
		if (is_array($menu_items)) {
			foreach ($menu_items as $menu_item) {
				$can_add_menu_item = true;
					
				if (elgg_in_context('menu_builder_manage')) {
					$menu_item['menu_builder_menu_name'] = $current_menu;
				} else {
					$access_id = $menu_item['access_id'];
					unset($menu_item['access_id']);
					switch ($access_id) {
						case ACCESS_PRIVATE:
							if (!elgg_is_admin_logged_in()) {
								$can_add_menu_item = false;
							}
							break;
						case \ColdTrick\MenuBuilder\Menu::ACCESS_LOGGED_OUT:
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
				
				if (empty($menu_item['target'])) {
					unset($menu_item['target']);
				}
				
				// strip out deprecated use of [wwwroot] as menu items will be normalized by default
				$menu_item['href'] = str_replace('[wwwroot]', '', $menu_item['href']);
				
				// add global replacable action tokens
				$is_action = (bool) elgg_extract('is_action', $menu_item, false);
				unset($menu_item['is_action']);
				if ($is_action && !elgg_in_context('menu_builder_manage')) {
					$menu_item['is_action'] = true;
				}
				
				// open in lightbox
				$lightbox = (bool) elgg_extract('lightbox', $menu_item, false);
				unset($menu_item['lightbox']);
				if ($lightbox) {
					$menu_item['link_class'] = ['elgg-lightbox'];
				}
				
				if (empty($menu_item['href'])) {
					$menu_item['href'] = false;
				} else {
					$menu_item['href'] = self::replacePlaceholders($menu_item['href']);
				}
				
				$return[] = \ElggMenuItem::factory($menu_item);
			}
		}
	
		// add 'new menu item' menu item
		if (elgg_in_context('menu_builder_manage')) {
			$return[] = \ElggMenuItem::factory([
				'name' => 'menu_builder_add',
				'icon' => 'plus',
				'text' => elgg_echo('menu_builder:edit_mode:add'),
				'href' => elgg_http_add_url_query_elements('ajax/view/menu_builder/edit_item', [
					'item_name' => 'menu_builder_add',
					'menu_name' => $current_menu,
				]),
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
	 * @param \Elgg\Event $event 'prepare', "menu:{$menu_name}"
	 *
	 * @return array
	 */
	public static function prepareAllMenu(\Elgg\Event $event) {
	
		// update order
		$ordered = [];
		$return = $event->getValue();
		
		if (isset($return['default'])) {
			foreach ($return['default'] as $menu_item) {
				$menu_item = self::orderMenuItem($menu_item, 2);
				$priority = $menu_item->getPriority();
				while (array_key_exists($priority, $ordered)) {
					$priority++;
				}
				
				$ordered[$priority] = $menu_item;
			}
		
			ksort($ordered);
			
			$return['default']->fill($ordered);
		}
		
		$menu = elgg_extract('default', $return, []);
	
		// prepare menu items for edit
		if (elgg_in_context('menu_builder_manage')) {
			self::prepareMenuItemsEdit($menu->getItems());
		}
	
		return $return;
	}
	
	/**
	 * Make sure all items are selected correctly
	 *
	 * @param \Elgg\Event $event 'prepare', 'all'
	 *
	 * @return array
	 */
	public static function prepareMenuSetSelected(\Elgg\Event $event) {
	
		if (strpos($event->getType(), 'menu:') !== 0) {
			return;
		}
	
		// set selected state on parent menu items
		$item = $event->getParam('selected_item');
		if (!$item instanceof \ElggMenuItem) {
			return;
		}
	
		while ($item && ($item = $item->getParent())) {
			$item->setSelected(true);
		}
	}
	
	/**
	 * Loads initially the site menu into the menu_builder config.
	 *
	 * @param \Elgg\Event $event 'prepare', 'menu:site'
	 *
	 * @return array
	 */
	public static function prepareSiteMenu(\Elgg\Event $event) {
		$plugin = elgg_get_plugin_from_id('menu_builder');
		if ($plugin->getSetting('menu_builder_default_imported', false)) {
			return;
		}
	
		$menu = new \ColdTrick\MenuBuilder\Menu('site');
		if (!empty($menu->getMenuConfig())) {
			// found an already existing menu config... do not import
			$plugin->setSetting('menu_builder_default_imported', time());
			return;
		}
		
		$menu->save();
		
		// remove potential existing menu items
		$plugin->unsetSetting('menu_site_config');
		
		$priority = 10;
		
		$return = $event->getValue();
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
	
			/* @var $item \ElggMenuItem */
			foreach ($items as $item) {
				$values = $item->getValues();
				$values['name'] = $item->getName();
				$values['priority'] = $priority;
				$values['parent_name'] = $parent_name;
				
				$menu->addMenuItem($values);
	
				$priority += 10;
				
				foreach ($item->getChildren() as $child_item) {
					$values = $child_item->getValues();
					$values['name'] = $child_item->getName();
					$values['priority'] = $priority;
					$values['parent_name'] = $item->getName();
					
					$menu->addMenuItem($values);
					
					$priority += 10;
				}
			}
		}
	
		$plugin->setSetting('menu_builder_default_imported', time());
	}
	
	/**
	 * Replaces placeholders in a string with actual information
	 *
	 * @param string $text the text to replace items in
	 *
	 * @return string
	 */
	protected static function replacePlaceholders(string $text): string {
		$user = elgg_get_logged_in_user_entity();
				
		// fill in username/userguid
		if ($user) {
			$text = str_replace('[username]', $user->username, $text);
			$text = str_replace('[userguid]', $user->guid, $text);
		} else {
			$text = str_replace('[username]', '', $text);
			$text = str_replace('[userguid]', '', $text);
		}
		
		return $text;
	}
	
	/**
	 * Prepares menu items to be edited
	 *
	 * @param \ElggMenuItem[] $menu array of \ElggMenuItem objects
	 *
	 * @return void
	 */
	protected static function prepareMenuItemsEdit(array $menu): void {
		foreach ($menu as $menu_item) {
			$text = $menu_item->getText();
	
			$name = $menu_item->getName();
			$menu_name = $menu_item->menu_builder_menu_name;
			
			if ($name == 'menu_builder_add') {
				continue;
			}
			
			$text .= elgg_format_element('span', [
				'title' => elgg_echo('edit'),
				'class' => ['elgg-lightbox', 'mls', 'menu-builder-action'],
				'data-colorbox-opts' => json_encode([
					'href' => elgg_http_add_url_query_elements('ajax/view/menu_builder/edit_item', [
						'item_name' => $name,
						'menu_name' => $menu_name,
					]),
				]),
			], elgg_view_icon('settings-alt'));
			
			$text .= elgg_format_element('span', [
				'title' => elgg_echo('delete'),
				'class' => ['mls', 'menu-builder-action'],
				'data-href' => elgg_generate_action_url('menu_builder/menu_item/delete', [
					'item_name' => $name,
					'menu_name' => $menu_name,
				]),
			], elgg_view_icon('delete'));

			$menu_item->setText($text);
			$menu_item->setHref(false);
	
			$children = $menu_item->getChildren();
			if ($children) {
				self::prepareMenuItemsEdit($children);
			}
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
	protected static function orderMenuItem(\ElggMenuItem $item, int $depth): \ElggMenuItem {
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
