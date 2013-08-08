<?php

require_once 'includes/cache.php';

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

function menus_get() {
	global $menus;
	if(!isset($menus)) {
		global $user, $language;
		$cache_id = "menus/{$language}/{$user->rid}";
		$menus = cache_get($cache_id);

		if(!$menus) {
			try {
				$items = db_select('menu_items', 'mi')
					->fields('mi')
					->condition(db_or()->condition('language', NULL)->condition('language', $language))
					->orderBy('menu')
					->orderBy('parent')
					->orderBy('weight')
					->execute()
					->fetchAllAssoc('mid', PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				// TODO : basic main menu with home and instal.php links.
				$items = array();
			}

			foreach($items as &$item) {
				if($item['language'] === NULL)
					$item['title'] = t($item['title']);
				if($item['parent'] && ($item['role'] === NULL || user_has_role($item['role'])))
					$items[$item['parent']]['items'][$item['mid']] =& $item;
			}

			foreach($items as &$item)
				$menus[$item['menu']]['items'][$item['mid']] =& $item;

			if(empty($menus))
				$menus = array();

			hook_invoke('menus', $menus);

			foreach($menus as $name => &$menu)
				foreach($menu['items'] as $key => &$item)
					if(!is_null($item['parent']))
						unset($menu['items'][$key]);
					else if($item['role'] !== NULL && !user_has_role((int) $item['role']))
						unset($menu['items'][$key]);
					else if($item['role'] === NULL && empty($item['items']))
						unset($menu['items'][$key]);

			cache_set($cache_id, $menus);
		}
	}
	return $menus;
}

function menu_add(array $item) {

	if(!empty($item['parent'])) {
		$query = db_select('menu_items', 'mi')
			->fields('mi', array('mid','menu'))
			->condition('path', $item['parent'], 'LIKE');

		if(isset($item['menu'])) {
			$query->condition('menu', $item['menu'], 'LIKE');
		}

		if(isset($item['language'])) {
			$query->condition(db_or()->condition('language', NULL)->condition('language', $item['language']));
			$query->orderBy('language', 'DESC');
		}

		$parent = $query->execute()->fetchAssoc();

		if($parent) {
			$item['parent'] =  (int) $parent['mid'];
			$item['menu'] = $parent['menu'];
		}
	}

	# Use default value instead of empty string
	foreach($item as $key => $value)
		if(empty($value))
			unset($item[$key]);

	$item += array(
		'menu' => 'main',
		'language' => NULL,
	);

	db_insert('menu_items')
		->fields($item)
		->execute();

	cache_delete('menus/%');
}

function menu_delete($path) {
	if(is_array($path))
		$path = $path['path'];

	db_delete('menu_items')
		->condition('path', $path, 'LIKE')
		->execute();

	cache_delete('menus/%');
}
