<?php
/**
 * Helper function are defined here
 */

/**
 * Returns an array of all the menu names that are managed by menu_builder
 *
 * @return array
 */
function menu_builder_get_managed_menus(): array {
	static $result;
	
	if (!isset($result)) {
		$result = (array) json_decode((string) elgg_get_plugin_setting('menu_names', 'menu_builder'), true);
	}
	
	return $result;
}
