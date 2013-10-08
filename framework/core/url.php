<?php

global $request_protocol;
global $request_port;
global $request_server;
global $request_realm;
global $request_folder;
global $request_script;
global $request_uri;
global $request_path;
global $request_segments;
global $request_base;
global $request_method;

$request_protocol = strtolower(@$_SERVER['HTTPS']) === 'on' ? 'https' : 'http';
$request_port     = @$_SERVER['SERVER_PORT']
	? ($request_protocol == 'http' && $_SERVER['SERVER_PORT'] == 80
		? '' # Standart HTTP port
		: ($request_protocol == 'https' && $_SERVER['SERVER_PORT'] == 443
			? '' # Standart HTTPS port
			: $_SERVER['SERVER_PORT'])) # Custom port
	: ''; # Default port
$request_server   = $_SERVER['SERVER_NAME'];
$request_realm    = $request_protocol."://".$request_server.($request_port ? ':'.$request_port : '');
$request_folder   = $request_realm.dirname($_SERVER['SCRIPT_NAME']);
$request_script   = basename($_SERVER['SCRIPT_NAME']);
$request_uri      = $_SERVER['REQUEST_URI'];
$request_path     = trim(!empty($_SERVER['PATH_INFO']) ? val($_SERVER['PATH_INFO'], 'index') : val($_REQUEST['q'], 'index'), '/');
$request_segments = explode('/', $request_path);
$request_base     = $request_folder . ($request_script !== 'index.php' ? $request_script : '');
$request_method   = $_SERVER['REQUEST_METHOD'];

function url_segment($n, $default = NULL) {
	global $request_segments;
	return isset($request_segments[$n]) ? $request_segments[$n] : $default;
}

function url($options) {

	if(!is_array($options))
		$options = array('path' => $options);

	url_setup($options);

	$base = NULL;
	if (isset($options['uri'])) {
		$base = $options['uri'];
	}
	else if(config('url.rewrite')) {
		global $request_folder;
		$base = $request_folder.$options['path'];
	}
	else {
		global $request_folder;
		global $request_script;

		if(empty($options['path']))
			$base = $request_folder;
		else
			$base = $request_folder.$request_script.'?q='.str_replace('?','&',$options['path']);
	}

	$query = '';
	if(!empty($options['query'])) {
		foreach((array) $options['query'] as $_k => $_v) {
			$query .= '&';
			if(is_int($_k))
				$query .= urlencode($_v);
			else
				$query .= urlencode($_k) . '=' . urlencode($_v);
		}
		if(!empty($query) && strpos($base, '?') === FALSE) {
			$query[0] = '?';
		}
	}

	$anchor = isset($options['anchor']) ? '#' . $options['anchor'] : '';

	return $base.$query.$anchor;
}

function url_setup(array &$options) {
	global $request_uri;

	if (!isset($options['path']) && !isset($options['uri'])) {
		$options['uri'] = $request_uri;
	}
	else if (isset($options['path'])) {
		$options['path'] = trim($options['path'], ' /');
		if($options['path'] === 'index') {
			$options['path'] = '';
		}
	}

	$options += array(
		'query' => array(),
		'anchor' => NULL,
		'title' => NULL,
		'attributes' => array(),
		'html' => TRUE,
		'active' => isset($options['path']) && url_is_current($options['path']),
		'view' => 'link',
	);
}

function url_is_active($path) {
	global $request_path;

	$path = trim($path, '/');

	return url_is_current($path) || strpos($request_path, $path) === 0;
}

function url_is_current($path) {
	global $request_path;

	if(!$request_path)
		return $path === '' || $path === 'index';

	if($request_path === $path)
		return TRUE;

	return FALSE;
}

function file_url($file) {
	global $request_folder;

	if($file_path = stream_resolve_include_path($file)) {
		return strtr($file_path, array(
			APP_ROOT => $request_folder . 'application/',
			FRAMEWORK_ROOT => $request_folder . 'framework/',
			SITE_ROOT => $request_folder,
		));
	}

	if(is_file(APP_ROOT.$file)) {
		return $request_folder . 'application/' . $file;
	}
	else if(is_file(FRAMEWORK_ROOT.$file)) {
		return $request_folder. 'framework/' . $file;
	}
	else if(is_file(SITE_ROOT.$file)) {
		return $request_folder . $file;
	}
	else {
		return NULL;
	}
}
