<?php

class Cache {
	public static function set($key, $value)
	{
		if(empty($key)) {
			return FALSE;
		}
		$key = Hook::call(HOOK_CACHE_KEY, $key);

		list($key, $value) = Hook::call(HOOK_CACHE_SET, array($key, $value));

		# TODO
		return FALSE;
	}
	
	public static function get($key)
	{
		if(empty($key)) {
			return FALSE;
		}
		$key = Hook::call(HOOK_CACHE_KEY, $key);

		if(self::exists($key)) {
			# TODO
			$value = NULL;

			list($key, $value) = Hook::call(HOOK_CACHE_GET, array($key, $value));
			
			return $value;
		}

		return FALSE;
	}
	
	public static function exists($key)
	{
		if(empty($key)) {
			return FALSE;
		}
		$key = Hook::call(HOOK_CACHE_KEY, $key);

		# TODO
		return FALSE;
	}
}