<?php

hook_register('menus', function(&$menus) {
	foreach($menus as $menu_name => &$menu) {
		if(!empty($menu['items'])) {
			foreach($menu['items'] as &$item) {
				if(@$item['path'] === 'scaffolding') {
					require_once 'includes/scaffolding.php';
					$tables = scaffolding_get_tables_menu();
					if(empty($item['items']))
						$item['items'] = $tables['items'];
					else
						$item['items'] = array_merge($item['items'], $tables['items']);
				}
			}
		}
	}
});

