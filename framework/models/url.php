<?php



$request_protocol = strtolower(@$_SERVER['HTTPS']) === 'on' ? 'https' : 'http';
$request_port     = @$_SERVER['SERVER_PORT']
	? ($request_protocol == 'http' && $_SERVER['SERVER_PORT'] == 80
		? '' # Standart HTTP port
		: ($request_protocol == 'https' && $_SERVER['SERVER_PORT'] == 443
			? '' # Standart HTTPS port
			: $_SERVER['SERVER_PORT'])) # Custom port
	: ''; # Default port
$request_server   = config('server.domain', val($_SERVER['HTTP_HOST'], $_SERVER['SERVER_NAME']));
$request_realm    = $request_protocol."://".$request_server.($request_port ? ':'.$request_port : '');
$request_folder   = $request_realm.dirname($_SERVER['SCRIPT_NAME']);
$request_script   = basename($_SERVER['SCRIPT_NAME']);
$request_uri      = $_SERVER['REQUEST_URI'];
$request_path     = trim(config('url.rewrite') === 'pathauto' ? val($_SERVER['PATH_INFO'], 'index') : val($_REQUEST['q'], 'index'), '/');
$request_segments = explode('/', $request_path);

function url_segment($n, $default = NULL) {
	global $request_segments;
	return isset($request_segments[$n]) ? $request_segments[$n] : $default;
}

function url($options) {

	if(!is_array($options))
		$options = array('path' => $options);

	url_setup($options);

	if (!empty($options['uri'])) {
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
			$base = $request_folder.$request_script.'?q='.$options['path'];
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

	$anchor = $options['anchor'] ? '#' . $options['anchor'] : '';

	return $base.$query.$anchor;
}

function url_setup(array &$options) {
	global $request_uri;

	if (!isset($options['path']) && empty($options['uri'])) {
		$options['uri'] = $request_uri;
	}
	else if (isset($options['path'])) {
		$options['path'] = trim($options['path'], ' /');
		if($options['path'] == config('site.home')) {
			$options['path'] = '';
		}
	}

	$options += array(
		'query' => array(),
		'anchor' => '',
		'title' => NULL,
		'attributes' => array(),
		'html' => TRUE,
		'active' => isset($options['path']) && url_is_active($options['path']),
	);
}

function url_is_active($path) {
	if(empty($path)) {
		return FALSE;
	}
	else {
		global $request_path;
		return strpos($request_path, $path) === 0 || strpos($request_path.'/index', $path) === 0;
	}
}

function file_url($file) {
	if(is_file(APP_ROOT.$file)) {
		global $request_folder;
		return $request_folder . 'application/' . $file;
	}
	else if(is_file(FRAMEWORK_ROOT.$file)) {
		global $request_folder;
		return $request_folder. 'framework/' . $file;
	}
	else if(is_file(SITE_ROOT.$file)) {
		global $request_folder;
		return $request_folder . $file;
	}
	else {
		return NULL;
	}
}
