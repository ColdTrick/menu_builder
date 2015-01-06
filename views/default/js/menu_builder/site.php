<?php
?>
//<script>
elgg.provide("elgg.menu_builder");

elgg.menu_builder.reorder_menu = function(menu) {
	var menu_items = new Array();

	$(menu).find(" > li.menu-builder-menu-item-sortable > a").each(function(index, elem){
		menu_items.push($(elem).attr("id"));
	});

	elgg.action("menu_builder/reorder", {
		data : {
			"elgg-menu-item" : menu_items
		}
	});
}

elgg.menu_builder.init = function() {
	$(".menu-builder-menu-item-sortable").parent().sortable({
		items: " > .menu-builder-menu-item-sortable",
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