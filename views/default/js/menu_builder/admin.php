<?php
?>
//<script>
elgg.provide("elgg.menu_builder");

elgg.menu_builder.init_admin = function() {
	$(".elgg-menu-item-menu-builder-add a").live("click", function(event){
		$(this).next().toggle();

		event.preventDefault();
	});
	
	$(".menu-builder-manage .elgg-icon-settings-alt").live("click", function(event){
		$(this).parent().next().toggle();

		event.preventDefault();
	});
}

elgg.register_hook_handler('init', 'system', elgg.menu_builder.init_admin);