<?php

# Expiration time of permanent cache data
define('CACHE_PERMANENT', 0);

# Load the database library
Load::db();

class Cache {
	public static function set($key, $data, $permanent = FALSE)
	{
		try {
			# Alter key or data
			list($key, $data) = Hook::call(HOOK_CACHE_SET, array($key, $data));

			# Cache lifetime
			$lifetime = config('cache/lifetime', CACHE_MAX_AGE);

			# Setup fields
			$fields = array(
				'serialized' => 0,
				'expire' => $permanent ? CACHE_PERMANENT : (REQUEST_TIME + $lifetime),
				'created' => REQUEST_TIME,
				);

			# Setup data fields
			if (!is_string($data)) {
				$fields['data'] = serialize($data);
				$fields['serialized'] = 1;
			}
			else {
				$fields['data'] = $data;
				$fields['serialized'] = 0;
			}

			# Insert into database
			$query = db_merge('cache')
				->key($key)
				->fields($fields)
				->execute();
		}
		catch (Exception $e) {
			# The database may not be available.
			var_dump($e); // XXX
		}
	}

	public static function get($key)
	{
		try {
			# Build the querry
			$query = db_select('cache', 'c')
				->fields('c', array('expire', 'created', 'serialized', 'data'))
				->condition('expire', REQUEST_TIME, '>');
			foreach($key as $k => $v)
				$query->condition($k, $v);

			# Execute the querry and get the first row
			$result = $query->execute();
			$cache = $result->fetch();

			# Check that there is one and only one row
			if($cache === FALSE OR $result->fetch() !== FALSE) {
				return NULL;
			}

			# Unszerialize data as needed
			if($cache->serialized) {			
				$cache->data = unserialize($cache->data);
			}

			# Alter data
			list($key, $cache) = Hook::call(HOOK_CACHE_GET, array($key, $cache));

			# Retrun the cache object
			return $cache;
		}
		catch (Exception $e) {
			# The database may not be available.
			return NULL;
		}
	}

	public static function clear($key = array())
	{
		self::garbageCollection();
		if(!empty($key)) {
			$query = db_delete('cache');
			foreach($key as $k => $v)
				$query->condition($k, $v);
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

	public static function isEmpty() {
		self::garbageCollection();
		$query = db_select('cache');
		$query->addExpression('1');
		$result = $query->range(0, 1)
			->execute()
			->fetchField();
		return empty($result);
	}
}