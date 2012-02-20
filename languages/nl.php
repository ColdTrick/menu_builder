<?php

	$language = array(
	
		/**
		 * Menu items and titles
		 */
	
		'menu_builder' => "Menu Builder",
	
		// item
		'item:object:menu_builder_menu_item' => "Menu Builder item",
	
		// views
		// edit
		'menu_builder:edit_mode:off' => "Normale weergave",
		'menu_builder:edit_mode:on' => "Bewerk weergave",
		'menu_builder:edit_mode:add' => "Klik om een nieuw menu item toe te voegen",
	
		// add
		'menu_builder:add:title' => "Nieuw menu item",
		'menu_builder:add:form:title' => "Titel",
		'menu_builder:add:form:url' => "URL",
		'menu_builder:add:form:parent' => "Hoofd menu item",
		'menu_builder:add:form:parent:toplevel' => "Toplevel menu item",
		'menu_builder:add:form:access' => "Wie kan dit menu item zien?",
		'menu_builder:add:access:admin_only' => "Enkel admins",
	
		// actions
		'menu_builder:actions:edit:error:input' => "Foutieve invoer",
		'menu_builder:actions:edit:error:entity' => "Opgegeven GUID kon niet worden gevonden",
		'menu_builder:actions:edit:error:subtype' => "Opgegeven GUID is geen menu item",
		'menu_builder:actions:edit:error:create' => "Er is een fout opgetreden, probeer het opnieuw",
		'menu_builder:actions:edit:error:parent' => "Verplaats eerst de submenu items voordat je het hoofdmenu item verplaatst.",
		'menu_builder:actions:edit:error:save' => "Er is een fout opgetreden, probeer het opnieuw",
		'menu_builder:actions:edit:success' => "Menu item opgeslagen",
	
		'menu_builder:actions:delete:success' => "Menu item verwijderd"
		
	);
					
	add_translation("nl",$language);
