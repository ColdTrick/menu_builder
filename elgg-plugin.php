<?php

use ColdTrick\MenuBuilder\Bootstrap;

require_once(__DIR__ . '/lib/functions.php');

return [
	'plugin' => [
		'version' => '10.0.2',
	],
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
	'events' => [
		'prepare' => [
			'all' => [
				'\ColdTrick\MenuBuilder\Menus::prepareMenuSetSelected' => [
					'priority' => 9999,
				],
			],
			'menu:site' => [
				'\ColdTrick\MenuBuilder\Menus::prepareSiteMenu' => [
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
	'view_options' => [
		'menu_builder/import' => ['ajax' => true],
		'menu_builder/edit_item' => ['ajax' => true],
	],
];
