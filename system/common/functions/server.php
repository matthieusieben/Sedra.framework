<?php

/**
 * If the server is behind a reverse proxy, we use the X-Forwarded-For header
 * instead of $_SERVER['REMOTE_ADDR'], which would be the IP address
 * of the proxy server, and not the client's.
 *
 * @return	IP address of client machine.
 */
function ip_address() {
	static $ip_address = NULL;

	if (!isset($ip_address))
	{
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
		{
			$ip_address_parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip_address = array_pop($ip_address_parts);
		}
		else
		{
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
	}

	return $ip_address;
}

/**
 * Determines if the current version of PHP is greater than the supplied value
 *
 * @access		public
 * @param		string
 * @return		bool
 */
function is_php($version = '5.0.0')
{
	static $is_php;
	$version = (string)$version;

	if (!isset($is_php[$version])) {
		$is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
	}

	return $is_php[$version];
}

/**
 * Unsets all disallowed global variables. See $allowed for what's allowed.
 */
function unset_globals() {
	if (ini_get('register_globals')) {

		$allowed = array(
			'_ENV' => TRUE,
			'_GET' => TRUE,
			'_POST' => TRUE,
			'_COOKIE' => TRUE,
			'_FILES' => TRUE,
			'_SERVER' => TRUE,
			'_REQUEST' => TRUE,
			'GLOBALS' => TRUE
			);

		foreach ($GLOBALS as $key => $value) {
			if (!isset($allowed[$key])) {
				unset($GLOBALS[$key]);
			}
		}
	}
}

/**
 * Look the include path for a file and returns its full path if found
 *
 * @param string $dir 
 * @param string $module 
 * @return string The file path or FALSE if not found
 */
function include_module( $dir, $module, $is_dir = NULL)
{
	if($is_dir === NULL OR $is_dir === FALSE)
	if($path = stream_resolve_include_path("$dir/$module.php")) {
		require_once $path;
		return TRUE;
	}

	if($is_dir === NULL OR $is_dir === TRUE)
	if($path = stream_resolve_include_path("$dir/$module/$module.php")) {
		require_once $path;
		return TRUE;
	}

	return FALSE;
}

/**
 * Mix between file_get_contents and include. This function prints the content
 * of $file without evaluating it (as include() would do).
 *
 * @param string $file A file in the current include_path.
 * @return void
 * @post The content of the file is printed.
 */
function include_source($file) {
	$orig = get_include_path();

	$bt = debug_backtrace();
	if(isset($bt[0]['file'])) {
		$caller_dir = dirname($bt[0]['file']).'/';
	
		$paths = explode(PATH_SEPARATOR, $orig);
		$new = '';
		foreach($paths as $p) {
			if($p === '.') {
				$p = $caller_dir;
			}
			elseif(substr($p,0,2) === './') {
				$p = substr_replace($p, $caller_dir, 0, 2);
			}
			$new .= empty($new) ? ($p) : (PATH_SEPARATOR.$p);
		}

		set_include_path($new);
	}

	if($path = stream_resolve_include_path($file)) {
		echo file_get_contents($path);
	}
	
	set_include_path($orig);
}
