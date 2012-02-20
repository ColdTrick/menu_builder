<?php 

	$title = get_input("title");
	$url = get_input("url");
	$access_id = (int) get_input("access_id", ACCESS_DEFAULT);
	$parent_guid = (int) get_input("parent_guid", 0);
	$guid = (int) get_input("guid");

	if(!empty($title) && !empty($url)){
		if(!empty($guid)){
			if($item = get_entity($guid)){
				if($item->getSubtype() != MENU_BUILDER_SUBTYPE){
					register_error(elgg_echo("menu_builder:actions:edit:error:subtype"));
					$item = null;
				}
			} else {
				register_error(elgg_echo("menu_builder:actions:edit:error:entity"));
			}
		} else {
			$item = new ElggObject();
			$item->subtype = MENU_BUILDER_SUBTYPE;
			$item->owner_guid = elgg_get_site_entity()->getGUID();
			
			$item->access_id = ACCESS_PUBLIC;
			
			$order = elgg_get_entities_from_metadata(array(
				"type" => "object",
				"subtype" => MENU_BUILDER_SUBTYPE,
				"metadata_name" => "parent_guid",
				"metadata_value" => $parent_guid,
				"count" => true
			));
			
			$item->parent_guid = $parent_guid;
			$item->order = $order;
			
			if(!$item->save()){
				register_error(elgg_echo("menu_builder:actions:edit:error:create"));
			}
		}
		
		if(!empty($item)){
			$save = true;
			
			$item->title = $title;
			$item->url = $url;
			
			$item->access_id = $access_id;
			
			if($item->parent_guid !== $parent_guid){
				$children = elgg_get_entities_from_metadata(array(
					"type" => "object",
					"subtype" => MENU_BUILDER_SUBTYPE,
					"metadata_name" => "parent_guid",
					"metadata_value" => $item->getGUID(),
					"count" => true
				));
				
				if(empty($children)){
					$item->parent_guid = $parent_guid;
					
					$order = elgg_get_entities_from_metadata(array(
						"type" => "object",
						"subtype" => MENU_BUILDER_SUBTYPE,
						"metadata_name" => "parent_guid",
						"metadata_value" => $parent_guid,
						"count" => true
					));
					
					$item->order = $order;
				} else {
					$save = false;
					register_error(elgg_echo("menu_builder:actions:edit:error:parent"));
				}
			}
			
			if($save){
				if($item->save()){
					system_message(elgg_echo("menu_builder:actions:edit:success"));
				} else {
					register_error(elgg_echo("menu_builder:actions:edit:error:save"));
				}
			}
		}
	} else {
		register_error(elgg_echo("menu_builder:actions:edit:error:input"));
	}

	forward(REFERER);
	