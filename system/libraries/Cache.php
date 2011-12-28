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


		
		$result = $query->execute();
		
		$num_rows = $query->countQuery()->execute()->fetchField();

		$value = NULL;

		list($key, $value) = Hook::call(HOOK_CACHE_GET, array($key, $value));
		return $value;
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

	private static function make_query($conditions) {
		$max_age = config('cache/age', CACHE_MAX_AGE);
		
		$query = db_select('cache', 'c');
		$query->condition('timestamp', $value, );
		
		foreach($conditions as $key => $value) {
			switch ($key) {
			case 'content': # text fields
				$query->condition($key, $value);
				break;

			default:
				$query->condition($key, $value);
				break;
			}
		}
		return $query;
	}

	public static function delete($key)
	{
		# TODO
	}
}