define(function(require) {
	var $ = require('jquery');
	var myvox = require('myvox');
	
	var already_sorted = false;
	
	$(document).on('click', '.menu-builder-manage .myvox-icon-delete', function(event) {
		if (!confirm(myvox.echo('deleteconfirm'))) {
			return false;
		} else {
			myvox.forward($(this).parent().data().href);
		}
	});
	
	$(document).on('click' ,'#menu-builder-add-menu', function(event) {
		var name = prompt(myvox.echo('menu_builder:admin:menu:add:internal_name'));
		if (!name) {
			return false;
		}
		
		myvox.forward(myvox.security.addToken('action/menu_builder/menu/edit?menu_name=' + name));
	});
	
	$(document).on('click', '.menu-builder-admin-tabs a', function(event) {
		var $tab = $(this);
		
		if ($tab.parent().hasClass('myvox-state-selected')) {
			return;
		}
		if ($tab.parent().attr('id') === 'menu-builder-add-menu') {
			return;
		}
		
		$tab.parents('.myvox-tabs').find('.myvox-state-selected').removeClass('myvox-state-selected');
		
		var rel = $tab.attr('rel');
		$tab.parent().addClass('myvox-state-selected');
		
		$('.menu-builder-admin-menu').addClass('hidden');
		$('.menu-builder-admin-menu[rel="' + rel + '"]').removeClass('hidden');
	});
	
	$('.menu-builder-admin-menu ul').sortable({
		connectWith: '.menu-builder-admin-menu ul',
		items: ' > li:not(.myvox-menu-item-menu-builder-add)',
		start: function(event, ui) {
			$('.menu-builder-admin-menu .myvox-menu-item-placeholder').removeClass('hidden');
		},
		stop: function(event, ui) {
			$('.menu-builder-admin-menu .myvox-menu-item-placeholder').addClass('hidden');
			already_sorted = false;
			
		},
		update: function(event, ui) {
			if (already_sorted) {
				return false;
			}

			var $item = $(ui.item);
			var item_name = getMenuItemNameFromClass($item.attr('class'));
			var parent_name = '';
			if (!$item.parent().parent().hasClass('menu-builder-admin-menu')) {
				parent_name = getMenuItemNameFromClass($item.parent().parent().attr('class'));
			}
			var menu_name = $item.parents('.menu-builder-admin-menu').attr('rel');			
			
			var items = [];
			$item.parent().find('>li:not(.myvox-menu-item-placeholder)').each(function(elem){
				var name = getMenuItemNameFromClass($(this).attr('class'));
				items.push(name);
			});
			
			myvox.action('menu_builder/menu/reorder', {
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
	
	var getMenuItemNameFromClass = function(class_text) {
		var result = class_text;
		
		classes = class_text.split(' ');
		$.each(classes, function(index, item) {
			if (item.search('myvox-menu-item-') === 0) {
				result = item.replace('myvox-menu-item-', '');
			}
		});
		
		return result;
	};
});