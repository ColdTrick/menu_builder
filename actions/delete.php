<?php
// jquery action to delete a menu item

if (elgg_is_admin_logged_in()) {
	$guid = get_input("guid");
	
	if (!empty($guid)) {
		if ($item = get_entity($guid)) {
			if ($item->getSubtype() == MENU_BUILDER_SUBTYPE) {
				$item->delete();
			}
		}
	}
}

exit();
