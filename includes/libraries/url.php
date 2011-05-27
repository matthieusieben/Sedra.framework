<?php

Url::init();

/**
 * Url
 */
class Url
{
	public static $query_string	= NULL;
	public static $uri			= NULL;
	public static $extension	= NULL;

	public static $segments		= array();

	public static function init()
	{
		// Get the query string
		$uri = isset($_GET['q']) ? trim( $_GET['q'], '/' ) : '';

		self::$query_string = $uri;

		// remove extension from $uri
		$path_parts = pathinfo($uri);
		if (isset($path_parts['extension']))
		{
			self::$extension = '.'.$path_parts['extension'];
			$uri = substr($uri, 0, -strlen(self::$extension));
		}

		// fetch segments
		foreach(explode('/', preg_replace('|/*(.+?)/*$|', "\\1", $uri)) as $val)
		{
			// Filter segments for security
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
	public static function segment( $pos = 0, $default = NULL )
	{
		return akon(self::$segments, $pos, $default);
	}

	/**
	 * Get a link to the current page (a correct version of $_SERVER['HTTP_REFERER'])
	 *
	 * @return	string	A link to the current page
	 */
	public static function current()
	{
		return self::make(self::$uri, $_GET);
	}

	/**
	 * Encode an url acordingly to the parsing method.
	 *
	 * @param	string	$url	A string composed of the controller and its arguments
	 * @param	string	$GET	An array of "GET" value to add into the URL
	 * @param	string	$id		The id string (#id) to add at the end of the uri (w/o the #).
	 * @return	string	The full url to the requested page
	 */
	public static function make($url = '', $GET = NULL, $id = NULL)
	{
		if ( isset($GET['q']) )
		{
			if( !$url ) $url = $GET['q'];
			unset($GET['q']);
		}

		$ext = config('uri/extension');
		$url = trim($url, '/');
		$page = empty($url) ? '' : $url.$ext;

		if (config('uri/rewrite')) {
			$query_string = $page.'?';
		}
		elseif ($page) {
			$query_string = 'index.php?q=/'.$page;
		}
		else {
			$query_string = '?';
		}

		foreach((array) $GET as $key => $value)
			if (is_string($key)) $query_string .= '&'.urlencode($key).'='.urlencode($value);
			else $query_string .= '&'.urlencode($value);

		$query_string = rtrim($query_string, '?') . ($id ? '#'.$id : '');

		return BASE_URL.$query_string;
	}

	public static function media( $file )
	{
		return self::file( 'media/'.$file );
	}

	public static function css( $file )
	{
		$u = self::file( 'css/'.$file );
		
		if(!$u) {
			$u = self::file( $file );
		}
		
		return $u;
	}

	public static function js( $file )
	{
		$u = self::file( 'js/'.$file );
		
		if(!$u) {
			$u = self::file( $file );
		}
		
		return $u;
	}

	public static function file( $path )
	{
		$file = str_replace( '/', DS, $path );

		if( is_file( BASE_DIR . $file ) )
			return BASE_URL . $path;

		return NULL;
	}

	public static function is_subpage_of($url)
	{
		if(($sl = strlen(self::$uri)) < strlen($url))
			return FALSE;
		// TODO : check next line
		if((($ss = substr(self::$uri,0,$sl)) === $url) && (!isset(self::$uri[$sl]) || self::$uri[$sl] === '/'))
			return TRUE;
		else
			return FALSE;
	}
}
