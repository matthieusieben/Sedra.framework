<?php

global $menus;
global $breadcrumb;

$menus['user']['items'] = array();
$menus['main']['items'] = array();
$menus['secondary']['items'] = array();
$breadcrumb['items'][] = array(
	'path' => 'index',
	'title' => t('Home'),
);

function menu_add($menu, $item) {
	global $menus;
	$menus[$menu]['items'][] = $item;
}

function breadcrumb_add($item) {
	global $breadcrumb;
	$breadcrumb['items'][] = $item;
}
