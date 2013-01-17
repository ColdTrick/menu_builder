<?php 

function menu_builder_site_menu_register($hook, $type, $return, $params) {
	$result = array();

	$options = array(
				"type" => "object",
				"subtype" => MENU_BUILDER_SUBTYPE,
				"limit" => false
	);
	if(!elgg_is_logged_in()){
		$options["wheres"] = array("e.access_id IN (" . ACCESS_PUBLIC . ", " . MENU_BUILDER_ACCESS_LOGGED_OUT . ")");
	}
	
	$entities = elgg_get_entities($options);

	if(empty($entities) && !elgg_get_plugin_setting("menu_builder_default_imported", "menu_builder") && elgg_is_admin_logged_in()){
		// create default menu items
			
		$priority = 10;
			
		if(count($return) > 5){
			// create more menu item
			$item = new ElggObject();
			$item->subtype = MENU_BUILDER_SUBTYPE;
			$item->owner_guid = elgg_get_site_entity()->getGUID();
			$item->container_guid = elgg_get_site_entity()->getGUID();

			$item->access_id = ACCESS_PUBLIC;

			$item->order = ($priority * count($return)) + 10;
			$item->title = elgg_echo("more");
			$item->parent_guid = 0;
			$item->save();

			$more_guid = $item->getGUID();
		}
			
		foreach($return as $key => $menu_item){
			$item = new ElggObject();
			$item->subtype = MENU_BUILDER_SUBTYPE;
			$item->owner_guid = elgg_get_site_entity()->getGUID();
			$item->container_guid = elgg_get_site_entity()->getGUID();

			$item->access_id = ACCESS_PUBLIC;

			if($key >= 5){
				$item->parent_guid = $more_guid;
			} else {
				$item->parent_guid = 0;
			}

			$item->order = $priority;

			$item->title = $menu_item->getText();
			$item->url = $menu_item->getHref();

			$item->save();

			$priority += 10;
				
		}
			
		elgg_set_plugin_setting("menu_builder_default_imported", time(), "menu_builder");
			
		// fetch items again
		$entities = elgg_get_entities($options);
	}

	if($entities){
		foreach($entities as $entity){

			$title = $entity->title;
			if($_SESSION["menu_builder_edit_mode"]){
				$title = $title . elgg_view_icon("settings-alt", "menu-builder-edit-menu-item");
			}

			$menu_options = array(
						"name" => $entity->getGUID(),
						"text" => $title, 
						"href" => $entity->getURL(),
						"priority" => $entity->order,
						"id" => $entity->getGUID()
			);
			
			if($entity->target == "_blank"){
				$menu_options["target"] = "_blank";
			}
			
			if(elgg_is_admin_logged_in()){
				$menu_options["item_class"] = "menu-builder-access-" . $entity->access_id;
			}
			
			if($entity->parent_guid){
				$menu_options["parent_name"] = $entity->parent_guid;
			}

			$result[] = ElggMenuItem::factory($menu_options);
		}
	}
	return $result;
}

function menu_builder_site_menu_prepare($hook, $type, $return, $params) {
	// update order
	$ordered = array();

	if(isset($return["default"])){
		foreach($return["default"] as $menu_item){
			$ordered[$menu_item->getPriority()] = $menu_item;
				
			if($children = $menu_item->getChildren()){
				// sort children
				$ordered_children = array();
	
				foreach($children as $child){
					$ordered_children[$child->getPriority()] = $child;
				}
				ksort($ordered_children);
	
				$menu_item->setChildren($ordered_children);
			}
				
			if($_SESSION["menu_builder_edit_mode"]){
				// add button
				$item = ElggMenuItem::factory(array(
											"name" => 'menu_builder_add', 
											"text" => elgg_view_icon("round-plus"), 
											"href" => '/menu_builder/edit?parent_guid=' . $menu_item->getName(),
											"class" => "menu_builder_add_link",
											"title" => elgg_echo("menu_builder:edit_mode:add")
				));
				$menu_item->addChild($item);
			}
		}
	}
	
	ksort($ordered);

	$return["default"] = $ordered;

	// add edit buttons
	if(elgg_is_admin_logged_in()){
		if($_SESSION["menu_builder_edit_mode"]){
			$item = ElggMenuItem::factory(array(
								"name" => 'menu_builder_add', 
								"text" => elgg_view_icon("round-plus"), 
								"href" => '/menu_builder/edit',
								"class" => "menu_builder_add_link",
								"title" => elgg_echo("menu_builder:edit_mode:add")
			));
			$return["default"][] = $item;
	
			$item = ElggMenuItem::factory(array(
								"name" => 'menu_builder_edit_mode', 
								"text" => elgg_view_icon("settings"), 
								"href" => '?menu_builder_edit_mode=off',
								"title" => elgg_echo("menu_builder:edit_mode:off")
			));
			$return["default"][] = $item;

			// add context switcher at the front of the menu
			$item = ElggMenuItem::factory(array(
								"name" => 'menu_builder_switch_context', 
								"text" => elgg_view_icon("eye"), 
								"href" => 'javascript:menu_builder_toggle_context();',
								"title" => elgg_echo("menu_builder:toggle_context")
			));
			array_unshift($return["default"], $item);
		
		} else {
			$item = ElggMenuItem::factory(array(
								"name" => 'menu_builder_edit_mode', 
								"text" => elgg_view_icon("settings"), 
								"href" => '?menu_builder_edit_mode=on',
								"title" => elgg_echo("menu_builder:edit_mode:on")
			));
			$return["default"][] = $item;
		}
	}

	return $return;
}

function menu_builder_write_access_hook($hook, $type, $return, $params) {
	$result = $return_value;
	
	if(elgg_in_context("menu_builder")){
		$result = array(
			ACCESS_PUBLIC => elgg_echo("PUBLIC"), 
			ACCESS_LOGGED_IN => elgg_echo("LOGGED_IN"), 
			MENU_BUILDER_ACCESS_LOGGED_OUT => elgg_echo("LOGGED_OUT"), 
			ACCESS_PRIVATE => elgg_echo("menu_builder:add:access:admin_only")
		);
	}
	
	return $result;
}