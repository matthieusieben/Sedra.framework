<?php

class Output {

	/**
	 * Delete a cookie
	 *
	 * @param	string	$k	The name of the cookie to delete
	 * @return	@see	Output::cookie()'s return value
	 */
	public static function cookie_del($k)
	{
		return self::cookie($k, NULL, -90000); # -25H
	}

	/**
	 * Store a value into a cookie
	 *
	 * @param	string	$name
	 * @param	mixed	$value
	 * @param	string	$validity
	 * @return	@see	php setcookie()'s return value
	 */
	public static function cookie($k, $v, $validity = NULL)
	{
		static $cookie_prefix, $cookie_path, $cookie_domain, $cookie_secure, $cookie_validity;

		if (!isset($cookie_validity)) {
			$cookie_prefix = config('cookie/prefix');
			$cookie_path = config('cookie/path');
			$cookie_domain = config('cookie/domain');
			$cookie_secure = config('cookie/secure');
			$cookie_validity = intval(config('cookie/validity'));
		}

		if (!is_numeric($validity)) {
			$validity = $cookie_validity;
		}

		$c = $cookie_prefix.$k;
		$e = REQUEST_TIME + $validity;

		if (is_php('5.2.0')) {
			return setcookie($c, serialize($v), $e, $cookie_path, $cookie_domain, $cookie_secure, TRUE);
		}
		else {
			return setcookie($c, serialize($v), $e, $cookie_path.'; HttpOnly', $cookie_domain, $cookie_secure);
		}
	}

	/**
	 * Store a value into the session array
	 *
	 * @param	string	$k
	 * @param	mixed	$v
	 */
	public static function session($k, $v)
	{
		Load::library('Session');
		$_SESSION[$k] = $v;
	}

	public static $content;

	/**
	 * Set the output content (ie html page)
	 *
	 * @param	string $content	
	 * @return	void
	 */
	public static function set($content)
	{
		self::$content = $content;
	}

	/**
	 * Print the output to the browser
	 *
	 * @return	void
	 */
	public static function render()
	{
		set_status_header(200);

		echo Hook::call(HOOK_RENDER, self::$content);
	}
	

}
