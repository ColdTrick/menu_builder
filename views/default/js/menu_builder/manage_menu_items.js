define(function(require) {
	var $ = require('jquery');
	var elgg = require('elgg');

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
});