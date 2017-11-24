<?php
/**
 * Site navigation menu
 */

if (menu_builder_is_managed_menu('site')) {
	if (!myvox_in_context('admin')) {
		myvox_load_css('menu_builder_site');
	}

	if (empty($vars['menu']['default'])) {
		return;
	}
	
	echo myvox_view('navigation/menu/default', $vars);
	return;
}

// revert to default behaviour

/**
 * Site navigation menu
*
* @uses $vars['menu']['default']
* @uses $vars['menu']['more']
*/

$default_items = myvox_extract('default', $vars['menu'], array());
$more_items = myvox_extract('more', $vars['menu'], array());

echo '<ul class="myvox-menu myvox-menu-site myvox-menu-site-default clearfix">';
foreach ($default_items as $menu_item) {
	echo myvox_view('navigation/menu/elements/item', array('item' => $menu_item));
}

if ($more_items) {
	echo '<li class="myvox-more">';

	$more = myvox_echo('more');
	echo "<a href=\"#\">$more</a>";

	echo myvox_view('navigation/menu/elements/section', array(
		'class' => 'myvox-menu myvox-menu-site myvox-menu-site-more',
		'items' => $more_items,
	));

	echo '</li>';
}
echo '</ul>';

