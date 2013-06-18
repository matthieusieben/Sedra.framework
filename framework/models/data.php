<?php

global $request_path;
global $controller, $language;
global $home_url, $site_data;
global $private_pages;

$home_url = config('site.home', 'index');
$logo_file = config('site.logo');
$logo_url = ($logo_url = file_url($logo_file)) ? $logo_url : $logo_file;

$site_data = array(
	'site_name' => config('site.name', 'Sedra Framework'),
	'site_logo' => $logo_url,
	'site_slogan' => config('site.slogan', NULL),
	'lang' => $language,
	'body' => array(
		'attributes' => array(
			'class' => array(
				'controller-'.$controller,
				'path-'.str_replace('/','-',$request_path),
			),
		),
	),
);

$site_data['menus']['main'] = array(
	'attributes' => array(
		'id' => 'main-menu',
	),
	'items' => array(
	),
);

$site_data['menus']['user'] = array(
	'attributes' => array(
		'id' => 'user-menu',
	),
	'items' => array(
		array(
			'title' => t('Account details'),
			'path' => 'account/index',
		),
		array(
			'title' => t('Change my credentials'),
			'path' => 'account/password',
		),
	),
);

$site_data['menus']['secondary'] = array(
	'attributes' => array(
		'id' => 'secondary-menu',
	),
	'items' => array(
	),
);

global $user;
if(isset($user)) {
	if(user_has_role(AUTHENTICATED_RID)) {
		if(config('scaffolding.enabled'))
		$site_data['menus']['secondary']['items'][] = array('path' => 'scaffolding', 'title' => t('Scaffolding'));

		$site_data['menus']['secondary']['items'][] = array('path' => 'account', 'title' => t('Account'));
		$site_data['menus']['secondary']['items'][] = array('path' => 'account/logout', 'title' => t('Logout'));
	} else {
		$site_data['menus']['secondary']['items'][] = array('path' => 'account/login', 'title' => t('Login'));
	}
}

$private_pages = array(
	'account',
	'account/',
	'error',
	'error/',
	'file/',
	'phpinfo',
);
