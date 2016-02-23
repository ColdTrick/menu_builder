<?php
/**
 * Site navigation menu
 */

if (menu_builder_is_managed_menu('site')) {
	if (!elgg_in_context('admin')) {
		elgg_load_css('menu_builder_site');
	}
	
	echo elgg_view('navigation/menu/default', $vars);
	return;
}

// revert to default behaviour

/**
 * Site navigation menu
*
* @uses $vars['menu']['default']
* @uses $vars['menu']['more']
*/

$default_items = elgg_extract('default', $vars['menu'], array());
$more_items = elgg_extract('more', $vars['menu'], array());

echo '<ul class="elgg-menu elgg-menu-site elgg-menu-site-default clearfix">';
foreach ($default_items as $menu_item) {
	echo elgg_view('navigation/menu/elements/item', array('item' => $menu_item));
}

if ($more_items) {
	echo '<li class="elgg-more">';

	$more = elgg_echo('more');
	echo "<a href=\"#\">$more</a>";

	echo elgg_view('navigation/menu/elements/section', array(
		'class' => 'elgg-menu elgg-menu-site elgg-menu-site-more',
		'items' => $more_items,
	));

	echo '</li>';
}
echo '</ul>';

