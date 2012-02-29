<?php 
if($_SESSION["menu_builder_edit_mode"]){
	
	elgg_load_js("lightbox");
	elgg_load_css("lightbox");
	
?>
<script type="text/javascript">
	function menu_builder_reorder_menu(menu){
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

	function menu_builder_toggle_context(){
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

	$(document).ready(function(){
		$(".menu_builder_add_link").fancybox({
				titleShow: false,
				"autoDimensions" : false,
				"width": 250,
				"height": 300
			});
		
		$(".menu-builder-edit-menu-item").live("click", function(event){
			var entity_guid = $(this).parent().attr("id");
			var edit_location = elgg.get_site_url() + "menu_builder/edit/" + entity_guid;
			
			$.fancybox({
				"href" : edit_location,
				"autoDimensions" : false,
				"width": 250,
				"height": 300
			});
			
			event.preventDefault();
		});

		$(".elgg-menu-site, .elgg-menu-site .elgg-child-menu").sortable({
			items: " > li:not(.elgg-menu-item-menu-builder-add, .elgg-menu-item-menu-builder-switch-context, .elgg-menu-item-menu-builder-edit-mode)",
			update: function(event, ui) {
				menu_builder_reorder_menu(this);
			}
		});
			
	});
</script>
<?php 
}
