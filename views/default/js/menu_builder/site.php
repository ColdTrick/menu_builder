<?php
?>
//<script>
elgg.provide("elgg.menu_builder");

elgg.menu_builder.reorder_menu = function(menu) {
	var menu_items = new Array();

	$(menu).find(" > li:not(.elgg-menu-item-menu-builder-add, .elgg-menu-item-menu-builder-switch-context, .elgg-menu-item-menu-builder-edit-mode) > a").each(function(index, elem){
		menu_items.push($(elem).attr("id"));
	});

	elgg.action("menu_builder/reorder", {
		data : {
			"elgg-menu-item" : menu_items
		}
	});
}

elgg.menu_builder.toggle_context = function() {
	$menu = $(".elgg-menu-site");
	if($menu.hasClass("menu-builder-context-all")){
		//step 4
		$menu.removeClass("menu-builder-context-all");
		elgg.system_message(elgg.echo("menu_builder:toggle_context:default"));
	} else if($menu.hasClass("menu-builder-context-logged-out")) {
		//step 3
		elgg.system_message(elgg.echo("menu_builder:toggle_context:all"));
		$menu.toggleClass("menu-builder-context-all menu-builder-context-logged-out");
	} else if($menu.hasClass("menu-builder-context-logged-in-normal")) {
		//step 2
		elgg.system_message(elgg.echo("menu_builder:toggle_context:logged_out"));
		$menu.toggleClass("menu-builder-context-logged-in-normal menu-builder-context-logged-out");
	} else {
		//step 1
		elgg.system_message(elgg.echo("menu_builder:toggle_context:normal_user"));
		$menu.addClass("menu-builder-context-logged-in-normal");
	}
}

elgg.menu_builder.menu_item_delete = function(guid) {
	if(guid){
		elgg.action("menu_builder/delete", {
			data: $('#menu_builder_add_form').serialize(),
			success: function(data){
				$("#" + guid).remove();
			}
		});
		
		$.colorbox.close();
	}
}

elgg.menu_builder.init = function() {
	$(".menu-builder-edit-menu-item").live("click", function(event){
		var entity_guid = $(this).parent().attr("id");
		var edit_location = elgg.get_site_url() + "menu_builder/edit/" + entity_guid;

		$.colorbox({
			"href" : edit_location,
			"innerWidth": 300
		});

		event.preventDefault();
	});

	$(".elgg-menu-site, .elgg-menu-site .elgg-child-menu").sortable({
		items: " > li:not(.elgg-menu-item-menu-builder-add, .elgg-menu-item-menu-builder-switch-context, .elgg-menu-item-menu-builder-edit-mode)",
		update: function(event, ui) {
			elgg.menu_builder.reorder_menu(this);
		}
	});

	// for everyone
	$(".elgg-menu-site .elgg-child-menu li").mouseover(function(elem) {
		var pos = $(this).position();
		var width = $(this).outerWidth();

		$(this).find("> .elgg-child-menu").css("left", (pos.left + width) + "px");
	});
}

elgg.register_hook_handler('init', 'system', elgg.menu_builder.init);