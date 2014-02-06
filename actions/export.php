<?php

$menu = elgg_get_entities(array(
	"type" => "object",
	"subtype" => MENU_BUILDER_SUBTYPE,
	"limit" => false
));

// make sure we have a menu to export
if (!$menu) {
	register_error(elgg_echo('menu_builder:actions:export:error:empty'));
	forward(REFERER);
}

// build a nice array to represent the menu
// note, guid sent along to determine parentage
// guid won't be the same when imported
$export = array();
foreach ($menu as $item) {
	$export[] = array(
		'guid' => $item->guid,
		'title' => $item->title,
		'url' => $item->url,
		'access_id' => $item->access_id,
		'parent_guid' => $item->parent_guid,
		'order' => $item->order,
		'target' => $item->target,
		'is_action' => $item->is_action
	);
}

// export the array as JSON in a txt file
$json = json_encode($export);

header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/json");
header("Content-Length: " . strlen($json));
header('Content-Disposition: attachment; filename="menu_builder_export.json"');
echo $json;

exit;
