<?php

/**
 * jQuery call to reorder menu items
 */

$order = get_input('elgg-menu-item');
	
if (empty($order)) {
	return;
}

foreach ($order as $index => $order_guid) {
	$item = get_entity($order_guid);
	
	if (empty($item)) {
		continue;
	}
	
	if ($item->getSubtype() !== MENU_BUILDER_SUBTYPE) {
		continue;
	}
	
	$item->order = $index;
}
