<?php

Url::_initialize();

/**
 * Url
 */
class Url
{
	public static $query_string	= NULL;
	public static $uri			= NULL;

	public static $segments		= array();

	public static function _initialize()
	{
		# Get the query string
		$uri = isset($_GET['q']) ? trim( $_GET['q'], '/' ) : '';

		self::$query_string = $uri;

		# fetch segments
		foreach(explode('/', preg_replace('|/*(.+?)/*$|', "\\1", $uri)) as $val)
		{
			# Filter segments for security
			$val = trim($val);
			if ($val != '')
			{
				self::$segments[] = $val;
			}
		}

		self::$uri = implode('/',self::$segments);
	}

	/**
	 * Get a uri segment
	 */
	public static function segment( $pos, $default = NULL )
	{
		return val(self::$segments[$pos], $default);
	}

	/**
	 * Get a link to the current page
	 *
	 * @return	string	A link to the current page
	 */
	public static function current()
	{
		return self::make(self::$uri, $_GET);
	}

	/**
	 * Encode an url accordingly to the parsing method.
	 *
	 * @param	string	$url	A string composed of the controller and its arguments
	 * @param	string	$GET	An array of "GET" value to add into the URL
	 * @param	string	$id		The id string (#id) to add at the end of the uri (w/o the #).
	 * @return	string	The full url to the requested page
	 */
	public static function make($url = '', $GET = NULL, $id = NULL)
	{
		if (config('uri/rewrite')) {
			$query_string = $url.'?';
		}
		elseif ($url) {
			$query_string = 'index.php?q=/'.$url;
		}
		else {
			$query_string = 'index.php?';
		}

		unset($GET['q']);
		foreach((array) $GET as $key => $value)
			if (is_string($key)) $query_string .= '&'.urlencode($key).'='.urlencode($value);
			else $query_string .= '&'.urlencode($value);

		$query_string = rtrim($query_string, '?') . ($id ? '#'.$id : '');

		$query_string = Hook::call('alter_query_string', $query_string);

		return BASE_URL.$query_string;
	}

	/**
	 * Get the url of a file located inside SITE_DIR.
	 *
	 * @param string $path 
	 * @return string the url
	 */
	public static function file( $path )
	{
		# Normalize $path
		$real_path = realpath($path);
		# If $real_path is into $dir
		if(strpos($real_path, SITE_DIR) === 0) {
			# get the location of $real_path relative to SITE_DIR
			$relative_path = substr($real_path, strlen(SITE_DIR));
			# make sure the url only contains '/' and no '\' (because of realpath)
			$relative_url = strtr($relative_path, DIRECTORY_SEPARATOR, '/');
			# Any hook to alter the path ?
			return Hook::call('alter_file_url', SITE_URL.$relative_url);
		}
		return NULL;
	}

	// XXX KEEP THIS ?
	public static function is_subpage_of($url)
	{
		if(strlen(self::$uri) < ($sl = strlen($url)))
			return FALSE;
		else
			return substr(self::$uri,0,$sl) === $url;
	}
}
