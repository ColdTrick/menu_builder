<?php

	$english = array(
	
		/**
		 * Menu items and titles
		 */
	
		'menu_builder' => "Menu Builder",
		'LOGGED_OUT' => "Logged out users",
		
		// item
		'item:object:menu_builder_menu_item' => "Menu Builder item",
	
		// views
		// edit
		'menu_builder:edit_mode:off' => "View mode",
		'menu_builder:edit_mode:on' => "Edit mode",
		'menu_builder:edit_mode:add' => "Click to add a new menu item",
	
		'menu_builder:toggle_context' => "Toggle context",
		'menu_builder:toggle_context:normal_user' => "Viewing the menu as a non-admin user",
		'menu_builder:toggle_context:logged_out' => "Viewing the menu for logged out users",
		'menu_builder:toggle_context:all' => "Viewing all menu items",
		'menu_builder:toggle_context:default' => "Viewing the menu as admin",
				
		// add
		'menu_builder:add:title' => "Add a new menu item",
		'menu_builder:add:form:title' => "Title",
		'menu_builder:add:form:url' => "URL",
		'menu_builder:add:form:parent' => "Parent menu item",
		'menu_builder:add:form:parent:toplevel' => "Toplevel menu item",
		'menu_builder:add:form:access' => "Who can see this menu item?",
		'menu_builder:add:access:admin_only' => "Admin only",
	
		// actions
		'menu_builder:actions:edit:error:input' => "Incorrect input to create/edit a menu item",
		'menu_builder:actions:edit:error:entity' => "The given GUID could not be found",
		'menu_builder:actions:edit:error:subtype' => "The givern GUID is not a menu item",
		'menu_builder:actions:edit:error:create' => "An error occured while creating the menu item, please try again",
		'menu_builder:actions:edit:error:parent' => "You can't move this menu item, because it has submenu items. Please move the submenu items first.",
		'menu_builder:actions:edit:error:save' => "An unknown error occured while saving the menu item, please try again",
		'menu_builder:actions:edit:success' => "The menu item was created/edited successfully",
	
		'menu_builder:actions:delete:success' => "The menu item was deleted successfully",
		
	);
					
	add_translation("en",$english);
