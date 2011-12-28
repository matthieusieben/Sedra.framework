<?php

{
	define('CACHE_PERMANENT', 0);

	Load::db();

	# Delete old entries
	debug(Cache::garbageCollection(), 'Cache items deleted');
}

class Cache {
	public static function set($key, $data, $permanent = FALSE)
	{
		if(empty($key)) {
			return FALSE;
		}
		$key = Hook::call(HOOK_CACHE_KEY, $key);

		list($key, $data) = Hook::call(HOOK_CACHE_SET, array($key, $data));

		$lifetime = config('cache/lifetime', CACHE_MAX_AGE);

		$fields = array(
			'serialized' => 0,
			'expire' => $permanent ? CACHE_PERMANENT : (REQUEST_TIME + $lifetime),
			'created' => REQUEST_TIME,
			);

		if (!is_string($data)) {
	      $fields['data'] = serialize($data);
	      $fields['serialized'] = 1;
	    }
	    else {
	      $fields['data'] = $data;
	      $fields['serialized'] = 0;
	    }

		try {
			$query = db_merge('cache')
				->key($key)
				->fields($fields)
				->execute();
		}
		catch (Exception $e) {
			# The database may not be available, so we'll ignore cache_set requests.
		}
	}

	public static function get($key)
	{
		if(empty($key)) {
			return FALSE;
		}
		$key = Hook::call(HOOK_CACHE_KEY, $key);

		$query = db_select('cache', 'c')
			->condition('expire', REQUEST_TIME, '>');
		self::query_add_keys($query, $key);
		self::query_add_fields($query);

		$result = $query->execute();
		$value = $result->fetchField();

		if($value === FALSE OR $result->fetchField() !== FALSE) {
			// If no or more than one item, return NULL.
			return FALSE;
		}

		$data = $value->serialized ? unserialize($value->data) : $value->data;

		list($key, $data) = Hook::call(HOOK_CACHE_GET, array($key, $data));
		return $data;
	}

	public static function clear($key = array())
	{
		# Delete specific entries
		if(!empty($key)) {
			$query = db_delete('cache');
			self::query_add_keys($query, $key);
			$query->execute();
			return $query->execute();
		}

		return 0;
	}

	public static function garbageCollection()
	{
		return db_delete('cache')
			->condition('expire', CACHE_PERMANENT, '<>')
			->condition('expire', REQUEST_TIME, '<=')
			->execute();
	}

	private static function &query_add_fields(&$query) {
		$query->fields('c', array('data', 'expire'));
		return $query;
	}

	private static function &query_add_keys(&$query, $conditions) {
		unset($condition['data'], $condition['expire']);
		foreach($conditions as $key => $value) {
			$query->condition($key, $value);
		}
		return $query;
	}
}