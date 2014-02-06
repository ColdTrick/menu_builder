<?php

$current_page = current_page_url();
$settings_url = elgg_get_site_url() . 'admin/plugin_settings/menu_builder';
$import_url = elgg_get_site_url() . 'admin/menu_builder/import';
$export_url = elgg_get_site_url() . 'action/menu_builder/export';

echo elgg_view('navigation/tabs', array(
	'tabs' => array(
		array(
			'text' => elgg_echo('settings'),
			'href' => $settings_url,
			'selected' => ($settings_url == $current_page)
		),
		array(
			'text' => elgg_echo('menu_builder:export:menu'),
			'href' => elgg_add_action_tokens_to_url($export_url),
			'selected' => false
		),
		array(
			'text' => elgg_echo('menu_builder:import:menu'),
			'href' => $import_url,
			'selected' => ($import_url == $current_page)
		)
	)
));
