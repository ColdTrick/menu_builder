<?php

namespace ColdTrick\MenuBuilder;

class Upgrade {

	/**
	 * Migrates old (pre MenuBuilder 2.0) menu entities to json
	 *
	 * @return void
	 */
	public static function migrateEntitiesToJSON() {
		$ia = elgg_set_ignore_access(true);
		
		$menu = new \ColdTrick\MenuBuilder\Menu('site');
		$menu->save();
		
		$options = [
			'type' => 'object',
			'subtype' => 'menu_builder_menu_item',
			'limit' => false,
		];
		
		$entities = elgg_get_entities($options);
		
		if (empty($entities)) {
			elgg_set_ignore_access($ia);
			return;
		}
		
		foreach ($entities as $menu_item) {
			$parent_name = null;
			$parent_guid = $menu_item->parent_guid;
			if ($parent_guid) {
				$parent = get_entity($parent_guid);
				if ($parent) {
					$parent_name = "menu_name_{$parent_guid}";
				}
			}
		
			$menu->addMenuItem([
				'name' => "menu_name_{$menu_item->guid}",
				'text' => $menu_item->title,
				'href' => $menu_item->url,
				'target' => $menu_item->target,
				'is_action' => $menu_item->is_action,
				'access_id' => $menu_item->access_id,
				'priority' => $menu_item->order,
				'parent_name' => $parent_name,
			]);
		}
		
		// delete entities need to do it afterwards as parents are not always available otherwise
		foreach($entities as $menu_item) {
			$menu_item->delete();
		}
		
		elgg_set_ignore_access($ia);
	}
}