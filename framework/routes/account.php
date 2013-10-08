<?php

global $routes;

$routes['account_signup'] = array(
	'url' => 'signup',
	'controller' => 'account/signup',
	'methods' => array('GET', 'POST'),
);

$routes['account_login'] = array(
	'url' => 'login',
	'controller' => 'account/login',
	'methods' => array('GET', 'POST'),
);

$routes['account_activate'] = array(
	'url' => 'account/activate/:key',
	'controller' => 'account/reset',
	'args' => array('action' => 'activate', 'key' => ':key'),
	'methods' => array('GET'),
);

$routes['account_reset'] = array(
	'url' => 'account/reset/:key',
	'controller' => 'account/reset',
	'args' => array('action' => 'reset', 'key' => ':key'),
	'methods' => array('GET'),
);

$routes['account_logout'] = array(
	'url' => 'logout',
	'controller' => 'account/logout',
	'methods' => array('GET'),
);

$routes['account_credentials'] = array(
	'url' => 'account/credentials',
	'controller' => 'account/credentials',
	'methods' => array('GET', 'POST'),
);

$routes['account_info'] = array(
	'url' => 'account',
	'controller' => 'account/info',
	'methods' => array('GET', 'POST'),
);
