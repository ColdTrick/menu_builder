<?php


function menu_builder_sort_menu_items($items, $parent_guid = 0){
	$result = false;

	if(array_key_exists($parent_guid, $items)){
		$result = array();
			
		foreach($items[$parent_guid] as $item){

			$children = menu_builder_sort_menu_items($items, $item->getGUID());

			$order = $item->order;
			if(empty($order)){
				$order = 0;
			}

			while(array_key_exists($order, $result)){
				$order++;
			}

			$result[$order] = array(
					"menu_item" => $item,
					"children" => $children
			);
		}
			
		ksort($result);
	}

	return $result;
}

function menu_builder_get_toplevel_menu_items(){
	global $CONFIG;

	$result = false;

	$options = array(
			"type" => "object",
			"subtype" => MENU_BUILDER_SUBTYPE,
			"owner_guid" => $CONFIG->site_guid,
			"limit" => false,
			"metadata_name" => "parent_guid",
			"metadata_value" => 0,
			"order_by_metadata" => array(
				"name" => "order", 
				"direction" => "ASC",
				"as" => "integer")
	);

	if($items = elgg_get_entities_from_metadata($options)){
		$result = array();
			
		foreach($items as $item){
			$result[$item->getGUID()] = $item->title;
		}
	}

	return $result;
}

function menu_builder_menu_item_is_selected($menu_item){
	$result = 0;
	
	$menu_item_url = $menu_item->getURL();
	if(!empty($menu_item_url) && $menu_item_url != "#"){
		$uri_info = parse_url($_SERVER['REQUEST_URI']);
		$item_info = parse_url($menu_item_url);
	
		// don't want to mangle already encoded queries but want to
		// make sure we're comparing encoded to encoded.
		// for the record, queries *should* be encoded
		$uri_params = array();
		$item_params = array();
		if (isset($uri_info['query'])) {
			$uri_info['query'] = html_entity_decode($uri_info['query']);
			$uri_params = elgg_parse_str($uri_info['query']);
		}
		if (isset($item_info['query'])) {
			$item_info['query'] = html_entity_decode($item_info['query']);
			$item_params = elgg_parse_str($item_info['query']);
		}
	
		$uri_info['path'] = trim($uri_info['path'], '/');
		$item_info['path'] = trim($item_info['path'], '/');
		
		// only if we're on the same path
		// can't check server because sometimes it's not set in REQUEST_URI
		if ($uri_info['path'] == $item_info['path']) {
			$result = 100;
		} elseif(!empty($uri_info['path']) && !empty($item_info['path'])) {
			if(strpos($uri_info['path'], $item_info['path']) === 0){
				// if menuitem url starts with
				$result = 100 - (strlen($uri_info['path']) - strlen($item_info['path']));
			}
		}
	}
		
	return $result;
}
