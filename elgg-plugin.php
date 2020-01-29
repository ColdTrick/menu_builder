<?php

use ColdTrick\MenuBuilder\Bootstrap;

define('MENU_BUILDER_ACCESS_LOGGED_OUT', -5);

require_once(__DIR__ . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'actions' => [
		'menu_builder/regen_site_menu' => ['access' => 'admin'],
		'menu_builder/menu/reorder' => ['access' => 'admin'],
		'menu_builder/menu/export' => ['access' => 'admin'],
		'menu_builder/menu/import' => ['access' => 'admin'],
		'menu_builder/menu/edit' => ['access' => 'admin'],
		'menu_builder/menu/delete' => ['access' => 'admin'],
		'menu_builder/menu_item/edit' => ['access' => 'admin'],
		'menu_builder/menu_item/delete' => ['access' => 'admin'],
	],
	'hooks' => [
		'prepare' => [
			'all' => [
				'\ColdTrick\MenuBuilder\MenuHooks::prepareMenuSetSelected' => [
					'priority' => 9999,
				],
			],
			'menu:site' => [
				'\ColdTrick\MenuBuilder\MenuHooks::prepareSiteMenu' => [
					'priority' => 900,
				],
			],
		],
	],
	'view_extensions' => [
		'admin.css' => [
			'css/menu_builder/admin.css' => [],
		],
	],
];
