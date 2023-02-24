define(['jquery', 'elgg', 'elgg/i18n', 'elgg/security', 'elgg/Ajax', 'jquery-ui/widgets/sortable'], function($, elgg, i18n, security, Ajax) {

	var already_sorted = false;
	var ajax = new Ajax();
	
	$(document).on('click', '.menu-builder-manage .elgg-icon-delete', function(event) {
		if (!confirm(i18n.echo('deleteconfirm'))) {
			return false;
		} else {
			elgg.forward($(this).parent().data().href);
		}
	});
	
	$(document).on('click' ,'#menu-builder-add-menu', function(event) {
		var name = prompt(i18n.echo('menu_builder:admin:menu:add:internal_name'));
		if (!name) {
			return false;
		}
		
		elgg.forward(security.addToken('action/menu_builder/menu/edit?menu_name=' + name));
	});
	
	$(document).on('click', '.menu-builder-admin-tabs a', function(event) {
		var $tab = $(this);
		
		if ($tab.parent().hasClass('elgg-state-selected')) {
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
			var item_name = getMenuItemNameFromClass($item.attr('class'));
			var parent_name = '';
			if (!$item.parent().parent().hasClass('elgg-menu-container')) {
				parent_name = getMenuItemNameFromClass($item.parent().parent().attr('class'));
			}
			
			var menu_name = $item.parents('.menu-builder-admin-menu').attr('rel');
			
			var items = [];
			$item.parent().find('>li:not(.elgg-menu-item-placeholder)').each(function(elem){
				var name = getMenuItemNameFromClass($(this).attr('class'));
				items.push(name);
			});
			
			ajax.action('menu_builder/menu/reorder', {
				data: {
					'menu_name': menu_name,
					'item_name': item_name,
					'parent_name': parent_name,
					'items': items,
				}
			});

			already_sorted = true;
		},
	});
	
	function getMenuItemNameFromClass(class_text) {
		var result = class_text;
		
		classes = class_text.split(' ');
		$.each(classes, function(index, item) {
			if (item.search('elgg-menu-item-') === 0) {
				result = item.replace('elgg-menu-item-', '');
			}
		});
		
		return result;
	}
});
