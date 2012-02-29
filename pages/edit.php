<?php 
	$guid = get_input("guid");
	
	if(elgg_is_admin_logged_in() &&	$_SESSION["menu_builder_edit_mode"]){

		if($guid && $menu_item = get_entity($guid)){
			$title = $menu_item->title;
			$url = $menu_item->url;
			$parent_guid = $menu_item->parent_guid;
			$access_id = $menu_item->access_id;
		} else {
			$guid = "";
			
			$parent_guid = get_input("parent_guid");
			
			if($parent_guid && ($parent = get_entity($parent_guid))){
				$access_id = $parent->access_id;
			} else {
				$access_id = ACCESS_LOGGED_IN;
			}
		}
		
		$form_body = "";
		$form_body .= elgg_view("input/hidden", array("name" => "guid", "value" => $guid));
		$form_body .= "<table><tr><td>";
		$form_body .= elgg_echo("menu_builder:add:form:title");
		$form_body .= "</td><td>";
		$form_body .= elgg_view("input/text", array("name" => "title", "value" => $title));
		$form_body .= "</td></tr><tr><td>";
		$form_body .= elgg_echo("menu_builder:add:form:url");
		$form_body .= "</td><td>";
		$form_body .= elgg_view("input/url", array("name" => "url", "value" => $url));
		$form_body .= "</td></tr></table>";
		
		if($main_items = menu_builder_get_toplevel_menu_items()){
			
			if(!empty($guid)){
				unset($main_items[$guid]);
			}
			
			if(!empty($main_items)){
				$form_body .= "<div>";
				$form_body .= elgg_echo("menu_builder:add:form:parent") . "<br />";
				$form_body .= elgg_view("input/dropdown", array("name" => "parent_guid", "value" => $parent_guid, "options_values" => array("0" => elgg_echo("menu_builder:add:form:parent:toplevel")) + $main_items));
				$form_body .= "</div>";
			}	
		}
					
		$form_body .= "<div>";
		$form_body .= elgg_echo("menu_builder:add:form:access") . "<br />";
		$form_body .= elgg_view("input/access", array("name" => "access_id", "value" => $access_id));
		$form_body .= "</div>";
		$form_body .= elgg_view("input/submit", array("value" => elgg_echo("save")));
		if(!empty($guid)){
			$delete_js = "onclick='if(confirm(\"" . elgg_echo("question:areyousure") . "\")){ menu_builder_menu_item_delete(" . $guid . "); }'";
			
			$form_body .= " ";
			$form_body .= elgg_view("input/button", array("type" => "button", "class" => "elgg-button-delete","value" => elgg_echo("delete"), "js" => $delete_js));
		}
		
		$form = elgg_view("input/form", array("action" => "action/menu_builder/edit", "body" => $form_body, "id" => "menu_builder_add_form"));

		echo elgg_view_module("info", elgg_echo("menu_builder:add:title"), $form);
		
		if(empty($guid)){
		?>
		<script type="text/javascript">
			var url_path = window.location.pathname;
			url_path = "[wwwroot]" + url_path.substr(1).replace("<?php echo elgg_get_logged_in_user_entity()->username;?>", "[username]")<?php if(elgg_get_page_owner_entity()){ ?>.replace("<?php echo page_owner_entity()->username; ?>", "[username]")<?php } ?>;

			var window_title = document.title.replace("<?php echo elgg_get_site_entity()->name. ": "; ?>", "");
			$("#menu_builder_add_form input[name='title']").val(window_title).focus();
			$("#menu_builder_add_form input[name='url']").val(url_path);
		</script>
		<?php 
		}
	} else {
		exit();
	}
	