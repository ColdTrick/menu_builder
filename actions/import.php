<?php

if ($_FILES['import']['error'] != UPLOAD_ERR_OK || !is_uploaded_file($_FILES['import']['tmp_name'])) {
	register_error(elgg_echo('menu_builder:actions:import:error:upload'));
	forward(REFERER);
}

if ($_FILES['import']['type'] != 'text/plain') {
	register_error(elgg_echo('menu_builder:actions:import:error:invalid:filetype'));
	forward(REFERER);
}

$json = file_get_contents($_FILES['import']['tmp_name']);

$menu = json_decode($json);

if (is_array($menu)) {

	// assume we're good at this point, delete current menu
	$old_menu = elgg_get_entities(array(
		"type" => "object",
		"subtype" => MENU_BUILDER_SUBTYPE,
		"limit" => false
	));

	if ($old_menu) {
		foreach ($old_menu as $old_item) {
			$old_item->delete();
		}
	}

	// now import new menu
	$parent_map = array();
	foreach ($menu as $key => $item) {
		$new_item = new ElggObject();
		$new_item->subtype = MENU_BUILDER_SUBTYPE;
		$new_item->owner_guid = elgg_get_site_entity()->getGUID();
		$new_item->container_guid = elgg_get_site_entity()->getGUID();
		$new_item->title = $item->title;
		$new_item->access_id = $item->access_id;

		if (!$new_item->save()) {
			register_error(elgg_echo("menu_builder:actions:edit:error:create"));
			continue;
		}

		// add metadata
		if ($item->target) {
			$new_item->target = $item->target;
		}

		$new_item->url = $item->url;
		$new_item->order = $item->order;
		$new_item->is_action = $item->is_action;

		// build a map to link the parents with the new guids
		$parent_map[$item->guid] = array('item' => $new_item, 'parent' => $item->parent_guid);
	}

	// now resolve the parents
	foreach ($parent_map as $array) {
		$new_item = $array['item'];
		$parent = $parent_map[$array['parent']]['item'];
		$new_item->parent_guid = $parent->guid ? $parent->guid : 0;
	}
} else {
	register_error(elgg_echo('menu_builder:actions:import:error:invalid:content'));
	forward(REFERER);
}

system_message(elgg_echo('menu_builder:actions:import:complete'));
forward(REFERER);