<?php

/**
 * Hooks for Menu Builder
 */


/**
 * Adds the menu items to the menus managed by menu_builder
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_all_menu_register($hook, $type, $return, $params) {
	$current_menu = $params["name"];
	$return = array(); // need to reset as there should be no other way to add menu items
	
	// fix menu name if needed
	$lang_key = "menu:" . $current_menu . ":header:default";
	if (elgg_echo($lang_key) == $lang_key) {
		add_translation(get_current_language(), array($lang_key => $current_menu));
	}
	
	// add configured menu items
	$menu_items = json_decode(elgg_get_plugin_setting("menu_" . $current_menu . "_config", "menu_builder"), true);
	if (is_array($menu_items)) {
		foreach ($menu_items as $menu_item) {
			if (elgg_in_context("menu_builder_manage")) {
				$menu_item["menu_builder_menu_name"] = $current_menu;
			}
			if (empty($menu_item["href"])) {
				// empty href's should not have a href set
				$menu_item["href"] = false;
			}
			$return[] = ElggMenuItem::factory($menu_item);
		}
	}
	
	// add 'new menu item' menu item
	if (elgg_in_context("menu_builder_manage")) {
		$item = ElggMenuItem::factory(array(
			"name" => 'menu_builder_add',
			"text" => "<strong>+</strong>&nbsp;&nbsp;" . elgg_echo("menu_builder:edit_mode:add"),
			"href" => "#",
			"link_class" => "elgg-lightbox",
			"menu_builder_menu_name" => $current_menu,
			"priority" => time()
		));
		
		$return[] = $item;
	}
	
	return $return;
}


/**
 * Makes menus managable if needed
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_all_menu_prepare($hook, $type, $return, $params) {
	if (elgg_in_context("menu_builder_manage")) {
		
		$parent_options = array();
		$menu = $return["default"];
		$parent_options = menu_builder_get_parent_options($menu);
		
		menu_builder_prepare_menu_items_edit($menu, $parent_options);
	}
}

function menu_builder_prepare_menu_items_edit($menu, $parent_options){
	foreach ($menu as $menu_item) {
		$text = "<a href='#'>" . $menu_item->getText();
		if ($menu_item->getName() != "menu_builder_add") {
			$text .= " " . elgg_view_icon("settings-alt");
		}
		$text .= "</a>";
			
		$text .= elgg_view("menu_builder/edit_item", array("menu_item" => $menu_item, "parent_options" => $parent_options));
		$menu_item->setText($text);
		$menu_item->setHref(false);
		
		$children = $menu_item->getChildren();
		if ($children) {
			menu_builder_prepare_menu_items_edit($children, $parent_options);
		}
	}
}

function menu_builder_get_parent_options($menu, $indent = 0) {
	$result = array();
	foreach($menu as $menu_item) {
		if ($menu_item->getName() == "menu_builder_add") {
			continue;
		}
		$text = str_repeat("-", $indent) . $menu_item->getText();
		$result[$menu_item->getName()] = $text;
		$children = $menu_item->getChildren();
		if ($children) {
			$children_options = menu_builder_get_parent_options($children, $indent + 1);
			$result = $result + $children_options;
		}
	}
	
	return $result;
}

/**
 * Adds the menu items to the site menu
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_site_menu_register($hook, $type, $return, $params) {
	$result = array();

	$options = array(
		"type" => "object",
		"subtype" => MENU_BUILDER_SUBTYPE,
		"limit" => false
	);

	if (!elgg_is_logged_in()) {
		$options["wheres"] = array("e.access_id IN (" . ACCESS_PUBLIC . ", " . MENU_BUILDER_ACCESS_LOGGED_OUT . ")");
	}

	$entities = elgg_get_entities($options);

	if (empty($entities) && !elgg_get_plugin_setting("menu_builder_default_imported", "menu_builder") && elgg_is_admin_logged_in()) {
		// create default menu items

		$priority = 10;
		$more_guid = null;

		if (count($return) > 5) {
			// create more menu item
			$item = new ElggObject();
			$item->subtype = MENU_BUILDER_SUBTYPE;
			$item->owner_guid = elgg_get_site_entity()->getGUID();
			$item->container_guid = elgg_get_site_entity()->getGUID();

			$item->access_id = ACCESS_PUBLIC;

			$item->order = ($priority * count($return)) + 10;
			$item->title = elgg_echo("more");
			$item->url = "#";
			$item->parent_guid = 0;
			$item->save();

			$more_guid = $item->getGUID();
		}

		foreach ($return as $key => $menu_item) {
			$item = new ElggObject();
			$item->subtype = MENU_BUILDER_SUBTYPE;
			$item->owner_guid = elgg_get_site_entity()->getGUID();
			$item->container_guid = elgg_get_site_entity()->getGUID();

			$item->access_id = ACCESS_PUBLIC;

			if ($key >= 5) {
				$item->parent_guid = $more_guid;
			} else {
				$item->parent_guid = 0;
			}

			$item->order = $priority;

			$item->title = $menu_item->getText();
			$item->url = str_replace(elgg_get_site_url(), "[wwwroot]", $menu_item->getHref());

			$item->save();

			$priority += 10;
		}

		elgg_set_plugin_setting("menu_builder_default_imported", time(), "menu_builder");

		// fetch items again
		$entities = elgg_get_entities($options);
	}

	if ($entities) {
		$parent_guids = array();
		foreach ($entities as $entity) {
			$parent_guids[] = $entity->getGUID();
		}

		foreach ($entities as $entity) {
			$parent_guid = $entity->parent_guid;
			$skip = false;

			while ($parent_guid) {
				if (!in_array($parent_guid, $parent_guids)) {
					$skip = true;
					break;
				}
				$parent_guid = get_entity($parent_guid)->parent_guid;
			}

			if ($skip) {
				continue;
			}

			$title = $entity->title;
			if (isset($_SESSION["menu_builder_edit_mode"])) {
				$title = $title . elgg_view_icon("settings-alt", "menu-builder-edit-menu-item");
			}

			$url = $entity->getURL();
			if ($entity->is_action) {
				$url = elgg_add_action_tokens_to_url($entity->getURL());
			}
			$menu_options = array(
						"name" => $entity->getGUID(),
						"text" => $title,
						"href" => $url,
						"priority" => $entity->order,
						"id" => $entity->getGUID()
			);

			if ($entity->target == "_blank") {
				$menu_options["target"] = "_blank";
			}

			if (elgg_is_admin_logged_in()) {
				$item_class = "menu-builder-access-" . $entity->access_id;
				if (isset($_SESSION["menu_builder_edit_mode"])) {
					$item_class .= " menu-builder-menu-item-sortable";
				}
				$menu_options["item_class"] = $item_class;
			}

			if ($entity->parent_guid) {
				$menu_options["parent_name"] = $entity->parent_guid;
			}

			$result[] = ElggMenuItem::factory($menu_options);
		}
	}

	return $result;
}

/**
 * Applies correct order to the menu items
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_site_menu_prepare($hook, $type, $return, $params) {
	// select parent menu items
	$item = elgg_extract('selected_item', $params);

	while ($item && ($item = $item->getParent())) {
		$item->setSelected(true);
	}
	// update order
	$ordered = array();

	if (isset($return["default"])) {
		foreach ($return["default"] as $menu_item) {

			$menu_item = menu_builder_order_menu_item($menu_item, 2);
			$priority = $menu_item->getPriority();
			while (array_key_exists($priority, $ordered)) {
				$priority++;
			}
			$ordered[$priority] = $menu_item;

			if (isset($_SESSION["menu_builder_edit_mode"])) {
				// add button
// 				$item = ElggMenuItem::factory(array(
// 											"name" => 'menu_builder_add',
// 											"text" => elgg_view_icon("round-plus"),
// 											"href" => '/menu_builder/edit?parent_guid=' . $menu_item->getName(),
// 											"link_class" => "center elgg-lightbox",
// 											"title" => elgg_echo("menu_builder:edit_mode:add")
// 				));
// 				$menu_item->addChild($item);
			}
		}
	}

	ksort($ordered);

	$return["default"] = $ordered;

	// add edit buttons
	if (elgg_is_admin_logged_in()) {
		if (isset($_SESSION["menu_builder_edit_mode"])) {
// 			$item = ElggMenuItem::factory(array(
// 								"name" => 'menu_builder_add',
// 								"text" => elgg_view_icon("round-plus"),
// 								"href" => '/menu_builder/edit',
// 								"link_class" => "center elgg-lightbox",
// 								"title" => elgg_echo("menu_builder:edit_mode:add")
// 			));
// 			$return["default"][] = $item;

// 			$item = ElggMenuItem::factory(array(
// 								"name" => 'menu_builder_edit_mode',
// 								"text" => elgg_view_icon("settings"),
// 								"href" => '?menu_builder_edit_mode=off',
// 								"title" => elgg_echo("menu_builder:edit_mode:off")
// 			));
// 			$return["default"][] = $item;

			// add context switcher at the front of the menu
// 			$item = ElggMenuItem::factory(array(
// 								"name" => 'menu_builder_switch_context',
// 								"text" => elgg_view_icon("eye"),
// 								"href" => 'javascript:elgg.menu_builder.toggle_context();',
// 								"title" => elgg_echo("menu_builder:toggle_context")
// 			));
// 			array_unshift($return["default"], $item);
		} else {
// 			$item = ElggMenuItem::factory(array(
// 								"name" => 'menu_builder_edit_mode',
// 								"text" => elgg_view_icon("settings"),
// 								"href" => '?menu_builder_edit_mode=on',
// 								"title" => elgg_echo("menu_builder:edit_mode:on")
// 			));
// 			$return["default"][] = $item;
		}
	}

	return $return;
}

/**
 * Replaces the options in the access dropdowns for menu items
 *
 * @param string  $hook   name of the hook
 * @param string  $type   type of the hook
 * @param unknown $return return value
 * @param unknown $params hook parameters
 *
 * @return array
 */
function menu_builder_write_access_hook($hook, $type, $return, $params) {
	$result = $return;

	if (elgg_in_context("menu_builder")) {
		$result = array(
			ACCESS_PUBLIC => elgg_echo("PUBLIC"),
			ACCESS_LOGGED_IN => elgg_echo("LOGGED_IN"),
			MENU_BUILDER_ACCESS_LOGGED_OUT => elgg_echo("LOGGED_OUT"),
			ACCESS_PRIVATE => elgg_echo("menu_builder:add:access:admin_only")
		);
	}

	return $result;
}

/**
 * Item url handler for Menu Builder
 *
 * @param string $hook_name    name of the hook
 * @param string $entity_type  type of the hook
 * @param string $return_value current return value
 * @param array  $params       hook parameters
 *
 * @return string
 */
function menu_builder_menu_item_url_handler($hook_name, $entity_type, $return_value, $params) {
	$result = $return_value;
	$entity = $params["entity"];

	if (empty($result) && elgg_instanceof($entity, "object", MENU_BUILDER_SUBTYPE)) {
		$result = "javascript:void(0);";

		if ($url = $entity->url) {
			// fill in site url
			$url = str_replace("[wwwroot]", elgg_get_site_url(), $url);
	
			// fill in username/userguid
			$user = elgg_get_logged_in_user_entity();
			if ($user) {
				$url = str_replace("[username]", $user->username, $url);
				$url = str_replace("[userguid]", $user->getGUID(), $url);
			} else {
				list($url) = explode("[username]", $url);
				list($url) = explode("[userguid]", $url);
			}
	
			$result = $url;
		}
	}
	
	return $result;
}
