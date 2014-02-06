<?php

$guid = (int) get_input("guid");

if (elgg_is_admin_logged_in() && isset($_SESSION["menu_builder_edit_mode"])) {

	elgg_push_context("menu_builder_form");
	
	if ($guid && $menu_item = get_entity($guid)) {
		$title = $menu_item->title;
		$url = $menu_item->url;
		$target = $menu_item->target;
		$parent_guid = (int) $menu_item->parent_guid;
		$access_id = (int) $menu_item->access_id;
		$is_action = $menu_item->is_action;
	} else {
		$guid = 0;
		$title = "";
		$url = "";
		$target = "";
		
		$parent_guid = (int) get_input("parent_guid");
		$is_action = '';
		
		if ($parent_guid && ($parent = get_entity($parent_guid))) {
			$access_id = (int) $parent->access_id;
		} else {
			$access_id = ACCESS_LOGGED_IN;
		}
	}
	
	$target_options = array("0" => elgg_echo("menu_builder:add:form:target:self"), "_blank" => elgg_echo("menu_builder:add:form:target:blank"));
	$is_action_options = array("name" => "is_action", "value" => 1);
	if ($is_action) {
		$is_action_options["checked"] = "checked";
	}
	
	$form_body = "";
	$form_body .= elgg_view("input/hidden", array("name" => "guid", "value" => $guid));
	$form_body .= "<table><tr><td>";
	$form_body .= elgg_echo("title");
	$form_body .= "</td><td>";
	$form_body .= elgg_view("input/text", array("name" => "title", "value" => $title));
	$form_body .= "</td></tr><tr><td>";
	$form_body .= elgg_echo("menu_builder:add:form:url");
	$form_body .= "</td><td>";
	$form_body .= elgg_view("input/url", array("name" => "url", "value" => $url));
	$form_body .= "</td></tr><tr><td>";
	$form_body .= elgg_echo('menu_builder:add:action:tokens');
	$form_body .= "</td><td>";
	$form_body .= elgg_view('input/checkbox', $is_action_options);
	$form_body .= "</td></tr><tr><td>";
	$form_body .= elgg_echo("menu_builder:add:form:target");
	$form_body .= "</td><td>";
	$form_body .= elgg_view("input/dropdown", array("name" => "target", "value" => $target, "options_values" => $target_options));
	$form_body .= "</td></tr></table>";
	
	$menu_items = menu_builder_get_parent_menu_select_options($guid);
	if (!empty($menu_items)) {
		$form_body .= "<div>";
		$form_body .= elgg_echo("menu_builder:add:form:parent") . "<br />";
		$form_body .= elgg_view("input/dropdown", array("name" => "parent_guid", "value" => $parent_guid, "options_values" => array("0" => elgg_echo("menu_builder:add:form:parent:toplevel")) + $menu_items));
		$form_body .= "</div>";
	}
				
	$form_body .= "<div>";
	$form_body .= elgg_echo("menu_builder:add:form:access") . "<br />";
	$form_body .= elgg_view("input/access", array("name" => "access_id", "value" => $access_id));
	$form_body .= "</div>";
	$form_body .= elgg_view("input/submit", array("value" => elgg_echo("save")));
	if (!empty($guid)) {
		$delete_js = "onclick='if(confirm(\"" . elgg_echo("question:areyousure") . "\")){ menu_builder_menu_item_delete(" . $guid . "); }'";
		
		$form_body .= " ";
		$form_body .= elgg_view("input/button", array("type" => "button", "class" => "elgg-button-delete","value" => elgg_echo("delete"), "js" => $delete_js));
	}
	
	$form = elgg_view("input/form", array("action" => "action/menu_builder/edit", "body" => $form_body, "id" => "menu_builder_add_form"));

	echo elgg_view_module("info", elgg_echo("menu_builder:add:title"), $form);
	
	if (empty($guid)) {
	?>
	<script type="text/javascript">
		var url_path = window.location.href;
		
		url_path = url_path.replace("<?php echo elgg_get_site_url(); ?>", "[wwwroot]");
		url_path = url_path.replace("<?php echo elgg_get_logged_in_user_entity()->username;?>", "[username]");
		<?php if (elgg_get_page_owner_entity()) {	?>
		url_path = url_path.replace("<?php echo elgg_get_page_owner_entity()->username; ?>", "[username]");
		<?php } ?>

		// regex makes sure the number isn't part of a larger number
		url_path = url_path.replace(/\b<?php echo elgg_get_logged_in_user_guid(); ?>\b/, "[userguid]");

		var window_title = document.title.replace("<?php echo elgg_get_site_entity()->name. ": "; ?>", "");
		$("#menu_builder_add_form input[name='title']").val(window_title).focus();
		$("#menu_builder_add_form input[name='url']").val(url_path);
	</script>
	<?php
	}
	
	elgg_pop_context();
} else {
	exit();
}
	