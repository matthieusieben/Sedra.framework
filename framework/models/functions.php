<?php

function config($item, $default = NULL) {
	global $config;
	return isset($config[$item]) ? $config[$item] : $default;
}

function &reg($k, $v=null) {
	global $registry;
	if(func_num_args() > 1) {
		$registry[$k] = $v;
	}
	elseif(!isset($registry[$k])) {
		$registry[$k] = NULL;
	}
	return $registry[$k];
}

function ip_address() {
	static $ip_address = NULL;
	if (!isset($ip_address)) {
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ip_address_parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip_address = array_pop($ip_address_parts);
		}
		else {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
	}
	return $ip_address;
}

function check_plain($text) {
	return htmlentities($text, ENT_QUOTES, 'UTF-8');
}

function decode_plain($text) {
	return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
}

function hash_base64($data) {
	$hash = base64_encode(hash('sha256', $data, TRUE));
	// Modify the hash so it's safe to use in URLs.
	return strtr($hash, array('+' => '-', '/' => '_', '=' => ''));
}

function random_bytes($count) {
	static $random_state, $bytes, $php_compatible;
	if (!isset($random_state)) {
		$random_state = print_r($_SERVER, TRUE);
		if (function_exists('getmypid')) {
			$random_state .= getmypid();
		}
		$bytes = '';
	}
	if (strlen($bytes) < $count) {
		if (!isset($php_compatible)) {
			$php_compatible = version_compare(PHP_VERSION, '5.3.4', '>=');
		}
		if ($fh = is_readable('/dev/urandom') ? fopen('/dev/urandom', 'rb') : NULL) {
			$bytes .= fread($fh, max(4096, $count));
			fclose($fh);
		}
		elseif ($php_compatible && function_exists('openssl_random_pseudo_bytes')) {
			$bytes .= openssl_random_pseudo_bytes($count - strlen($bytes));
		}
		while (strlen($bytes) < $count) {
			$random_state = hash('sha256', microtime() . mt_rand() . $random_state);
			$bytes .= hash('sha256', mt_rand() . $random_state, TRUE);
		}
	}
	$output = substr($bytes, 0, $count);
	$bytes = substr($bytes, $count);
	return $output;
}

function random_salt($length = 32) {
	return substr(hash('sha256', random_bytes(64)), 0, min(64, max(0, $length)));
}

function set_status_header($code = 200)
{
	if (headers_sent()) return;

	$stati = array(
		200	=> 'OK',
		201	=> 'Created',
		202	=> 'Accepted',
		203	=> 'Non-Authoritative Information',
		204	=> 'No Content',
		205	=> 'Reset Content',
		206	=> 'Partial Content',

		300	=> 'Multiple Choices',
		301	=> 'Moved Permanently',
		302	=> 'Found',
		304	=> 'Not Modified',
		305	=> 'Use Proxy',
		307	=> 'Temporary Redirect',

		400	=> 'Bad Request',
		401	=> 'Unauthorized',
		403	=> 'Forbidden',
		404	=> 'Not Found',
		405	=> 'Method Not Allowed',
		406	=> 'Not Acceptable',
		407	=> 'Proxy Authentication Required',
		408	=> 'Request Timeout',
		409	=> 'Conflict',
		410	=> 'Gone',
		411	=> 'Length Required',
		412	=> 'Precondition Failed',
		413	=> 'Request Entity Too Large',
		414	=> 'Request-URI Too Long',
		415	=> 'Unsupported Media Type',
		416	=> 'Requested Range Not Satisfiable',
		417	=> 'Expectation Failed',
		418 => 'I\'m a teapot',

		500	=> 'Internal Server Error',
		501	=> 'Not Implemented',
		502	=> 'Bad Gateway',
		503	=> 'Service Unavailable',
		504	=> 'Gateway Timeout',
		505	=> 'HTTP Version Not Supported'
	);

	if ( !isset($stati[$code]) )
	{
		fatal('No status text available. Please check your status code number or supply your own message text.');
	}

	$text = $stati[$code];

	if (substr(php_sapi_name(), 0, 3) === 'cgi')
	{
		header("Status: {$code} {$text}", TRUE);
	}
	else
	{
		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if ( substr($server_protocol,0,4) === 'HTTP' )
		{
			header($server_protocol." {$code} {$text}", TRUE, $code);
		}
		else
		{
			header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
	}
}

function redirect($path = 'index', $query = NULL, $http_response_code = 302) {
	set_status_header($http_response_code);

	if(isset($_GET['redirect'])) {
		$path = $_GET['redirect'];
	}

	if(is_array($path)) {
		$options = $path + array('query' => $query);
	} else {
		$options = array('path' => $path, 'query' => $query);
	}

	$url = url($options);

	hook_invoke('shutdown');

	header('Location: '. $url, TRUE, $http_response_code);

	echo @theme('redirect', array('url' => $url));

	exit();
}

function ob_get_clean_all() {
	$output = '';

	# Previous output
	while(ob_get_level() > 0) {
		$output = ob_get_clean() . $output;
	}

	return $output;
}

function is_email($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
}

function load_controller($__controller) {

	if(file_exists($__file = APP_CONTROLLERS . $__controller . '.php'))
		return require($__file);

	if(file_exists($__file = APP_CONTROLLERS . $__controller . '/index.php'))
		return require($__file);

	if(file_exists($__file = FRAMEWORK_CONTROLLERS . $__controller . '.php'))
		return require($__file);

	if(file_exists($__file = FRAMEWORK_CONTROLLERS . $__controller . '/index.php'))
		return require($__file);

	return show_404(config('devel') ? t('Unable to load the controller "@controller"', array('@controller'=>$__controller)) : NULL);
}

function load_model($__model) {
	static $__loaded = array();

	if(isset($__loaded[$__model]))
		return $__loaded[$__model];

	if(file_exists($__file = APP_MODELS . $__model . '.php'))
		return $__loaded[$__model] = require_once($__file);

	if(file_exists($__file = FRAMEWORK_MODELS . $__model . '.php'))
		return $__loaded[$__model] = require_once($__file);

	throw new FrameworkException(t('Cannot load model @model', array('@model' => $__model)));
}

function load_library($__library, $__required = TRUE) {
	static $__loaded = array();

	if(isset($__loaded[$__library]))
		return $__loaded[$__library];

	if(file_exists($__file = APP_LIBRARIES . $__library . '.php'))
		return $__loaded[$__library] = require($__file);

	if(file_exists($__file = APP_LIBRARIES . $__library . '/bridge.php'))
		return $__loaded[$__library] = require($__file);

	if(file_exists($__file = FRAMEWORK_LIBRARIES . $__library . '.php'))
		return $__loaded[$__library] = require($__file);

	if(file_exists($__file = FRAMEWORK_LIBRARIES . $__library . '/bridge.php'))
		return $__loaded[$__library] = require($__file);

	if($__required)
		throw new FrameworkException(t('Cannot load library @library', array('@library' => $__library)));

	return FALSE;
}

function show_403($msg = NULL) {
	throw new FrameworkException($msg ? $msg : t('You are not allowed to access this page.'), 403);
}

function show_404($msg = NULL) {
	throw new FrameworkException($msg ? $msg : t('The requested page does not exist.'), 404);
}

/**
 * Get a value if it is set of a default value otherwise.
 *
 * @param mixed $value
 * @param mixed $default
 * @return isset(value) ? value : default
 */
function val(&$value, $default = NULL) {
	return isset($value) ? $value : $default;
}

function super() {
	$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	$path = realpath($caller[0]['file']);
	return strtr($path, array(APP_ROOT => FRAMEWORK_ROOT));
}
