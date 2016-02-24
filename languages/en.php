<?php

return array(

	'menu_builder' => "Menu Builder",

	// item
	'item:object:menu_builder_menu_item' => "Menu Builder item",
		
	// admin
	'menu_builder:admin:menu:list' => "Managable menus",
	'menu_builder:admin:menu:add' => "New menu",
	'menu_builder:admin:menu:add:internal_name' => "Internal menu name",
	'menu_builder:admin:menu:placeholder' => "This is a placeholder",

	// views
	// edit
	'menu_builder:edit_mode:add' => "Click to add a new menu item",

	// add
	'menu_builder:add:form:url' => "URL",
	'menu_builder:add:form:target' => "Target",
	'menu_builder:add:form:target:self' => "Same Window",
	'menu_builder:add:form:target:blank' => "New Window",
	'menu_builder:add:form:parent' => "Parent menu item",
	'menu_builder:add:form:access' => "Who can see this menu item?",
	'menu_builder:add:access:admin_only' => "Admin only",
	'menu_builder:add:action:tokens' => "Action",

	// import
	'menu_builder:import:title' => "Importing menu items for %s",
	'menu_builder:import:warning' => "Importing a site menu will permanently and irreversably remove the current
	  menu configuration.  Proceed with caution.<br><br>
	After importing, check the links as hard-coded urls from another system may be still point at a wrong domain if not using
	relative links, or if exported from a system in a subdirectory and imported into a different subdirectory.
	",
	'menu_builder:import:help' => "Upload a file from an exported menu",

	// actions
	'menu_builder:actions:missing_name' => "Missing menu name",
	
	'menu_builder:actions:edit:error:input' => "Incorrect input to create/edit a menu item",
	'menu_builder:actions:edit:success' => "The menu item was created/edited successfully",

	'menu_builder:actions:delete:success' => "The menu item was deleted successfully",

	'menu_builder:actions:export:error:empty' => "No menu items available to be exported",

	'menu_builder:actions:import:error:upload' => "There was an error with the file upload",
	'menu_builder:actions:import:error:invalid:content' => "There was an error with the file contents",
	'menu_builder:actions:import:complete' => "Menu import has been completed",

	// settings
	'menu_builder:settings:htmlawed:filter' => "Filter url and titles through htmlawed?",
	'menu_builder:settings:regen_site_menu' => "With this link you can choose to regenerate the site menu. This will override the existing menu items registered on the site menu.",
	'menu_builder:settings:regen_site_menu:button' => "Regen now!",

);
