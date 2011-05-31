<?php

class Input {
	
	public static function post($k, $d = NULL)
	{
		return val($_POST[$k], $d);
	}
	
	public static function get($k, $d = NULL)
	{
		return val($_GET[$k], $d);
	}
	
	public static function cookie($k, $d = NULL)
	{
		$c = config('cookie/prefix').$k;
		return isset($_COOKIE[$c]) ? unserialize($_COOKIE[$c]) : $d;
	}
	
	public static function session($k, $d = NULL)
	{
		Load::library('Session');
		return val($_SESSION[$k], $d);
	}
}