<?php 
if($_SESSION["menu_builder_edit_mode"]){
	
	elgg_load_js("lightbox");
	elgg_load_css("lightbox");
	
?>
<script type="text/javascript">
	function menu_builder_reorder_menu(menu){
		var menu_items = new Array();
		
		$(menu).find(" > li:not(.elgg-menu-item-menu-builder-add, .elgg-menu-item-menu-builder-edit-mode) > a").each(function(index, elem){
			menu_items.push($(elem).attr("id"));
		});
		
		elgg.action("menu_builder/reorder", {
			data : {
				"elgg-menu-item" : menu_items
			}
		});
	}

	$(document).ready(function(){
		$(".menu_builder_add_link").fancybox({
				titleShow: false,
				"autoDimensions" : false,
				"width": 250,
				"height": 300
			});
		
		$(".menu-builder-edit-menu-item").live("click", function(event){
			var entity_guid = $(this).parent().attr("rel");
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
			items: " > li:not(.elgg-menu-item-menu-builder-add, .elgg-menu-item-menu-builder-edit-mode)",
			update: function(event, ui) {
				menu_builder_reorder_menu(this);
			}
		});
			
	});
</script>
<?php 
}
