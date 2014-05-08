<?php
?>
//<script>
elgg.provide("elgg.menu_builder");

elgg.menu_builder.init_admin = function() {
	$(".elgg-menu-item-menu-builder-add a").live("click", function(event) {
		$(this).next().toggle();
		event.preventDefault();
	});
	
	$(".menu-builder-manage .elgg-icon-settings-alt").live("click", function(event) {
		$(this).parent().parent().next("form").toggle();
		event.preventDefault();
	});
	$(".menu-builder-manage .elgg-icon-delete").live("click", function(event) {
		if (!confirm(elgg.echo("deleteconfirm"))) {
			return false;
		} else {
			$form = $(this).parent().parent().next("form");
			var name = $form.find("input[name='name']").val();
			var menu_name = $form.find("input[name='menu_name']").val();
			elgg.action("menu_builder/menu_item/delete", {
				data : {
					"name" : name,
					"menu_name" : menu_name,
				},
				success: function(data) {
					location.reload();
				}
			});
		}
	});

	$(".menu-builder-menu-delete").live("submit", function(event) {
		if (!confirm(elgg.echo("deleteconfirm"))) {
			return false;
		}		
	});
}

elgg.register_hook_handler('init', 'system', elgg.menu_builder.init_admin);