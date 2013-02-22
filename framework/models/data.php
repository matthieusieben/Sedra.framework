<?php

global $request_path, $controller, $language;
global $home_url, $site_data;

$home_url = config('site.home', 'index');
$logo_file = config('site.logo');
$logo_url = ($logo_url = file_url($logo_file)) ? $logo_url : $logo_file;

$site_data = array(
	'site_name' => config('site.name', 'My website'),
	'site_logo' => $logo_url,
	'site_slogan' => config('site.slogan', NULL),
	'site_url' => $home_url,
	'lang' => $language,
	'body' => array(
		'attributes' => array(
			'class' => array(
				'controller-'.$controller,
				'path-'.str_replace('/','-',$request_path),
			),
		),
	),
	'menus' => array(
		'main' => array(
			'attributes' => array(
				'id' => 'main-menu',
			),
			'items' => array(
				/* array(
					'title' => t('Home'),
					'path' => $home_url,
				), */
			),
		),
	),
);
