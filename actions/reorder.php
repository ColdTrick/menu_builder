<?php

/**
 * jQuery call to reorder menu items
 */

$order = get_input("elgg-menu-item");
	
if (!empty($order)) {
	foreach ($order as $index => $order_guid) {
		if ($item = get_entity($order_guid)) {
			if (($item->getSubtype() == MENU_BUILDER_SUBTYPE)) {
				$item->order = $index;
			}
		}
	}
}
