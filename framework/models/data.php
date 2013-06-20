<?php

global $site_data;
global $private_pages;

global $request_path, $controller, $language;
$site_data = array(
	'site_name' => config('site.name', 'Sedra Framework'),
	'site_logo' => ($logo_url = file_url(config('site.logo'))) ? $logo_url : config('site.logo'),
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

$site_data['menus']['nav-main'] = array(
	'attributes' => array(
		'id' => 'nav-main',
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

$site_data['menus']['nav-secondary'] = array(
	'attributes' => array(
		'id' => 'nav-secondary',
	),
	'items' => array(
	),
);

load_model('user');
if(user_has_role(AUTHENTICATED_RID)) {
	if(config('scaffolding.enabled'))
	$site_data['menus']['nav-secondary']['items'][] = array('path' => 'scaffolding', 'title' => t('Scaffolding'));

	$site_data['menus']['nav-secondary']['items'][] = array(
		'path' => 'account',
		'title' => t('Account'),
		'icon' => '<i class="icon-user icon-white"></i>',
		'sub' => array(
			'items' => array(
				array(
					'title' => t('My account'),
					'path' => 'account/index',
				),
				array(
					'title' => t('Logout'),
					'path' => 'account/logout',
				),
			),
		),
	);
} else {
	$site_data['menus']['nav-secondary']['items'][] = array(
		'title' => t('Login'),
		'path' => 'account/login',
		'icon' => '<i class="icon-user icon-white"></i>',
	);
}

$private_pages = array(
	'account',
	'account/',
	'error',
	'error/',
	'file/',
	'phpinfo',
);
