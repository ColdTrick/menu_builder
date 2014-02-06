<?php

function menu_builder_get_toplevel_menu_items(){
	
	$result = false;

	$options = array(
			"type" => "object",
			"subtype" => MENU_BUILDER_SUBTYPE,
			"owner_guid" => elgg_get_site_entity()->getGUID(),
			"limit" => false,
			"metadata_name" => "parent_guid",
			"metadata_value" => 0,
			"order_by_metadata" => array(
				"name" => "order",
				"direction" => "ASC",
				"as" => "integer")
	);

	if ($items = elgg_get_entities_from_metadata($options)) {
		$result = array();
			
		foreach ($items as $item) {
			$result[$item->getGUID()] = $item->title;
		}
	}

	return $result;
}
