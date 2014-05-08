<?php
$parent_options = $vars["parent_options"];
$menu_item = elgg_extract("menu_item", $vars);

$menu_name = $menu_item->menu_builder_menu_name;

if ($menu_item->getName() == "menu_builder_add") {
	$text = "";
	$href = "";
	$access_id = ACCESS_PUBLIC;
	$target = 0;
	$is_action = false;
	$name = null;
	$parent_name = null;
	$priority = null;
	
	if (elgg_get_config("walled_garden")) {
		$access_id = ACCESS_LOGGED_IN;
	}
} else {
	$name = $menu_item->getName();
	$text = $menu_item->getText();
	$href = str_replace(elgg_get_site_url(), "", $menu_item->getHref());
	$access_id = $menu_item->access_id;
	$target = $menu_item->target;
	$is_action = (boolean) $menu_item->is_action;
	$parent_name = $menu_item->getParentName();
	$priority = $menu_item->getPriority();
}

$target_options = array("0" => elgg_echo("menu_builder:add:form:target:self"), "_blank" => elgg_echo("menu_builder:add:form:target:blank"));
$access_options = array(
	ACCESS_PUBLIC => elgg_echo("PUBLIC"),
	ACCESS_LOGGED_IN => elgg_echo("LOGGED_IN"),
	MENU_BUILDER_ACCESS_LOGGED_OUT => elgg_echo("LOGGED_OUT"),
	ACCESS_PRIVATE => elgg_echo("menu_builder:add:access:admin_only")
);

$form_body = "";

$form_body .= elgg_view("input/hidden", array("name" => "name", "value" => $name));
$form_body .= elgg_view("input/hidden", array("name" => "menu_name", "value" => $menu_name));
$form_body .= "<table><tr><td>";

$form_body .= "<label>" . elgg_echo("title") . "</label>";
$form_body .= "</td><td>";

$form_body .= elgg_view("input/text", array(
	"name" => "text",
	"value" => $text
));

$form_body .= "</td></tr><tr><td>";
$form_body .= "<label>" . elgg_echo("menu_builder:add:form:url") . "</label>";
$form_body .= "</td><td>";

$form_body .= elgg_view("input/text", array(
	"name" => "href",
	"value" => $href
));

$form_body .= "</td></tr><tr><td>";
$form_body .= "<label>" . elgg_echo("menu_builder:add:form:priority") . "</label>";
$form_body .= "</td><td>";

$form_body .= elgg_view("input/text", array(
	"name" => "priority",
	"value" => $priority
));

$form_body .= "</td></tr><tr><td>";
$form_body .= "<label>" . elgg_echo("menu_builder:add:form:access") . "</label>";
$form_body .= "</td><td>";

$form_body .= elgg_view("input/access", array(
	"name" => "access_id",
	"value" => $access_id,
	"options_values" => $access_options
));

$form_body .= "</td></tr><tr><td>";
$form_body .= "<label>" . elgg_echo("menu_builder:add:form:target") . "</label>";
$form_body .= "</td><td>";

$form_body .= elgg_view("input/select", array(
	"name" => "target",
	"value" => $target,
	"options_values" => $target_options
));

if ($parent_options) {
	array_unshift($parent_options, "");
	$form_body .= "</td></tr><tr><td>";
	$form_body .= "<label>" . elgg_echo("menu_builder:add:form:parent") . "</label>";
	$form_body .= "</td><td>";
	
	$form_body .= elgg_view("input/select", array(
			"name" => "parent_name",
			"value" => $parent_name,
			"options_values" => $parent_options
	));
}

$form_body .= "</td></tr><tr><td>";
$form_body .= "<label>" . elgg_echo("menu_builder:add:action:tokens") . "</label>";
$form_body .= "</td><td>";

$form_body .= elgg_view("input/checkbox", array(
	"name" => "is_action",
	"value" => 1,
	"checked" => $is_action 
));

$form_body .= "<td/></tr></table>";

$form_body .= elgg_view("input/submit", array("value" => elgg_echo("save")));

echo elgg_view("input/form", array("action" => "action/menu_builder/menu_item/edit", "body" => $form_body, "class" => "hidden"));