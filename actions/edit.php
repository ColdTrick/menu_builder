<?php

$filter = true;
if (elgg_get_plugin_setting('htmlawed_filter', 'menu_builder') == 'no') {
	$filter = false;
}

$title = get_input("title", '', $filter);
$translated_titles = (array) get_input("translated_titles", '', $filter);
$url = get_input("url", '', $filter);
$target = get_input("target");
$access_id = (int) get_input("access_id", ACCESS_DEFAULT);
$parent_guid = (int) get_input("parent_guid", 0);
$guid = (int) get_input("guid");
$is_action = get_input("is_action");

if (!empty($title) && !empty($url)) {
	if (!empty($guid)) {
		if ($item = get_entity($guid)) {
			if ($item->getSubtype() != MENU_BUILDER_SUBTYPE) {
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
		$item->container_guid = elgg_get_site_entity()->getGUID();

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

		if (!$item->save()) {
			register_error(elgg_echo("menu_builder:actions:edit:error:create"));
		}
	}

	if (!empty($item)) {
		$item->title = $title;
		$item->url = $url;
		$item->is_action = $is_action;

		if ($target) {
			$item->target = $target;
		} else {
			unset($item->target);
		}

		$item->access_id = $access_id;

		if ($item->parent_guid !== $parent_guid) {

			$item->parent_guid = $parent_guid;

			$order = elgg_get_entities_from_metadata(array(
				"type" => "object",
				"subtype" => MENU_BUILDER_SUBTYPE,
				"metadata_name" => "parent_guid",
				"metadata_value" => $parent_guid,
				"count" => true
			));

			$item->order = $order;
		}
		
		// translated titles
		$json_data = array();
		if (!empty($translated_titles)) {
			foreach ($translated_titles as $country_code => $translation) {
				if (!empty($translation)) {
					$json_data[$country_code] = $translation;
				}
			}
		}
		
		if (!empty($json_data)) {
			$json_data = json_encode($json_data);
			$item->translated_titles = $json_data;
		} else {
			unset($item->translated_titles);
		}		
		
		if ($item->save()) {
			system_message(elgg_echo("menu_builder:actions:edit:success"));
		} else {
			register_error(elgg_echo("menu_builder:actions:edit:error:save"));
		}
	}
} else {
	register_error(elgg_echo("menu_builder:actions:edit:error:input"));
}

forward(REFERER);
