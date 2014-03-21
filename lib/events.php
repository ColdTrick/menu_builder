<?php

/**
 * Events for Menu Builder
 */

/**
 * Delete child menu items when a menu item gets deleted
 *
 * @param string     $event       event
 * @param string     $entity_type type
 * @param ElggEntity $object      object
 *
 * @return void
 */
function menu_builder_delete_event_handler($event, $entity_type, $object) {
	if (!empty($object) && elgg_is_admin_logged_in()) {
		if (elgg_instanceof($object, "object", MENU_BUILDER_SUBTYPE)) {
			$options = array(
					"type" => "object",
					"subtype" => MENU_BUILDER_SUBTYPE,
					"limit" => false,
					"metadata_name" => "parent_guid",
					"metadata_value" => $object->getGUID()
			);

			if ($children = elgg_get_entities_from_metadata($options)) {
				foreach ($children as $child) {
					$child->delete();
				}
			}
		}
	}
}
