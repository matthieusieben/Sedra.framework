<?php

load_model('database');

global $menus;
global $breadcrumb;

$breadcrumb['items'][] = array(
	'path' => 'index',
	'title' => t('Home'),
);

function breadcrumb_add($item) {
	global $breadcrumb;
	$breadcrumb['items'][] = $item;
}

function &menu_get($menu_name = NULL) {
	global $menus;
	if(!isset($menus)) {
		$items = db_select('menu_items', 'mi')->fields('mi')->orderBy('menu')->orderBy('parent')->orderBy('weight')->execute()->fetchAllAssoc('mid', PDO::FETCH_ASSOC);
		foreach($items as &$item) {
			$item['title'] = t($item['title']);
			if($item['parent'] && ($item['role'] === NULL || user_has_role($item['role'])))
				$items[$item['parent']]['items'][$item['mid']] =& $item;
		}

		foreach($items as &$item)
			if(($item['role'] === NULL && !empty($item['items'])) || user_has_role($item['role']))
				if(is_null($item['parent']))
					$menus[$item['menu']]['items'][$item['mid']] =& $item;
	}
	if($menu_name) {
		$null = NULL;
		return isset($menus[$menu_name]) ? $menus[$menu_name] : $null;
	}
	return $menus;
}
