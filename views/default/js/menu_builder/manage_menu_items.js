define(function(require) {
	var $ = require('jquery');
	var elgg = require('elgg');
	
	var already_sorted = false;

	$(document).on('click', '.elgg-menu-item-menu-builder-add a', function(event) {
		$(this).next().toggle();
		event.preventDefault();
	});
	
	$(document).on('click', '.menu-builder-manage .elgg-icon-settings-alt', function(event) {
		$(this).parent().parent().next('form').toggle();
		event.preventDefault();
	});
	
	$(document).on('click', '.menu-builder-manage .elgg-icon-delete', function(event) {
		if (!confirm(elgg.echo('deleteconfirm'))) {
			return false;
		} else {
			$form = $(this).parent().parent().next('form');
			
			var name = $form.find('input[name="name"]').val();
			var menu_name = $form.find('input[name="menu_name"]').val();
			
			elgg.action('menu_builder/menu_item/delete', {
				data : {
					'name' : name,
					'menu_name' : menu_name,
				},
				success: function(data) {
					location.reload();
				}
			});
		}
	});
	
	$(document).on('click' ,'#menu-builder-add-menu', function(event) {
		var name = prompt(elgg.echo('menu_builder:admin:menu:add:internal_name'));
		if (!name) {
			return false;
		}
		
		elgg.forward(elgg.security.addToken('action/menu_builder/menu/edit?menu_name=' + name));
	});
	
	$(document).on('click', '.menu-builder-admin-tabs a', function(event) {
		var $tab = $(this);
		
		if ($tab.parent().hasClass('elgg-state-selected')) {
			return;
		}
		if ($tab.parent().attr('id') === 'menu-builder-add-menu') {
			return;
		}
		
		$tab.parents('.elgg-tabs').find('.elgg-state-selected').removeClass('elgg-state-selected');
		
		var rel = $tab.attr('rel');
		$tab.parent().addClass('elgg-state-selected');
		
		$('.menu-builder-admin-menu').addClass('hidden');
		$('.menu-builder-admin-menu[rel="' + rel + '"]').removeClass('hidden');
	});
	
	$('.menu-builder-admin-menu ul').sortable({
		connectWith: '.menu-builder-admin-menu ul',
		items: ' > li:not(.elgg-menu-item-menu-builder-add)',
		start: function(event, ui) {
			$('.menu-builder-admin-menu .elgg-menu-item-placeholder').removeClass('hidden');
		},
		stop: function(event, ui) {
			$('.menu-builder-admin-menu .elgg-menu-item-placeholder').addClass('hidden');
			already_sorted = false;
			
		},
		update: function(event, ui) {
			if (already_sorted) {
				return false;
			}

			var $item = $(ui.item);
			var item_name = $item.attr('class').replace('elgg-menu-item-', '');
			var parent_name = '';
			if (!$item.parent().parent().hasClass('menu-builder-admin-menu')) {
				parent_name = $item.parent().parent().attr('class').replace('elgg-menu-item-', '');
			}
			var menu_name = $item.parents('.menu-builder-admin-menu').attr('rel');			
			
			var items = [];
			$item.parent().find('>li:not(.elgg-menu-item-placeholder)').each(function(elem){
				var name = $(this).attr('class').replace('elgg-menu-item-', '');
				items.push(name);
			});
			
			elgg.action('menu_builder/menu/reorder', {
				data : {
					'menu_name' : menu_name,
					'item_name' : item_name,
					'parent_name' : parent_name,
					'items' : items,
				}
			});

			already_sorted = true;
		},
	});
	
//	$('.menu-builder-admin-menu li[class!="elgg-menu-item-menu-builder-add"][class!="elgg-menu-item-placeholder"]').on('mouseover', function(event) {
//		console.log($(this));
//		$(this).find(' > ul > .elgg-menu-item-placeholder').toggleClass('hidden');
//	});
});