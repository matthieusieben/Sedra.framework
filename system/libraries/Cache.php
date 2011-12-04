<?php

class Cache {
	public static function set(&$o, $value)
	{
		if(!self::get_key($o)) {
			return FALSE;
		}

		return FALSE;
	}
	
	public static function get(&$o)
	{
		if(!self::get_key($o)) {
			return FALSE;
		}

		return FALSE;
	}
	
	public static function exists(&$o)
	{
		if(!self::get_key($o)) {
			return FALSE;
		}

		return FALSE;
	}

	public static function get_key(&$o)
	{
		if(!isset($o->cache_key)) {
			self::set_key($o);
		}

		return $o->cache_key;
	}

	public static function set_key(&$o)
	{
		$o->cache_key = self::key($o);
	}

	public static function key($o) {
		$flags = isset($o->cache_flags) ? $o->cache_flags : NULL;

		if(($flags & CACHE_ENABLED) !== CACHE_ENABLED)
		{
			return FALSE;
		}
		
		$id = array(
			'class' => get_class($o),
		);
		
		if( isset($o->id) )
		{
			$id['id'] = $o->id;
		}
		
		if(($flags & CACHE_LEVEL_METHOD) === CACHE_LEVEL_METHOD)
		{
			$id['method'] = $o->method;
		}

		if(($flags & CACHE_LEVEL_URL) === CACHE_LEVEL_URL)
		{
			$id['url'] = Url::current();
		}

		if(($flags & CACHE_LEVEL_USER) === CACHE_LEVEL_USER) {
			Load::user();
			$user = User::current();
			
			$id['uid'] = $user->uid;
			
			if($user->uid === ANONYMOUS_UID) {
				$id['lang'] = $user->language;
			}
		}

		if(($flags & CACHE_LEVEL_ROLE) === CACHE_LEVEL_ROLE) {
			Load::user();
			$user = User::current();
		
			$id['rid'] = $user->rid;
			$id['lang'] = $user->language;
		}

		if(($flags & CACHE_LEVEL_LANG) === CACHE_LEVEL_LANG) {
			Load::user();
			$user = User::current();
		
			$id['lang'] = $user->language;
		}

		return $id;
	}
}