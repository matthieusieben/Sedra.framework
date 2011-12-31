<?php

# Expiration time of permanent cache data
define('CACHE_PERMANENT', 0);

# Load the database library
Load::db();

/**
 * Caching class allowing to store and get objects from the database.
 */
class Cache {

	/**
	 * Set a value in the datablase
	 *
	 * @param array $key The set of keys identifying this object
	 * @param mixed $data The data to store
	 * @param bool $permanent Should this data expire after a certain period of time?
	 * @return void
	 */
	public static function set($key, $data, $permanent = FALSE)
	{
		try {
			if(empty($key)) {
				return;
			}

			# Alter key or data
			list($key, $data) = Hook::call(HOOK_CACHE_SET, array($key, $data));

			# Cache lifetime
			$lifetime = config('cache/lifetime', CACHE_DEFAULT_LIFETIME);

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
		}
	}

	/**
	 * Get content from the cache
	 *
	 * @param array $key The set of keys identifying the object to get
	 * @return sdtClass containing the data and its creation and expiration timestamps
	 */
	public static function get($key)
	{
		try {
			if(empty($key)) {
				return NULL;
			}

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
			
			$cache->key = $key;

			# Alter data
			$cache = Hook::call(HOOK_CACHE_GET, $cache);

			# Retrun the cache object
			return $cache;
		}
		catch (Exception $e) {
			# The database may not be available.
			return NULL;
		}
	}

	/**
	 * Remove data from the cache
	 *
	 * @param array $key The set of keys identifying the object to remove. Empty array will remove all.
	 * @return int The number of items deleted
	 */
	public static function clear($key = array())
	{
		self::garbageCollection();
		$query = db_delete('cache');
		foreach($key as $k => $v)
			$query->condition($k, $v);
		$query->execute();
		return $query->execute();
	}

	/**
	 * Clear outdated data from the cache
	 *
	 * @return void
	 */
	public static function garbageCollection()
	{
		return db_delete('cache')
			->condition('expire', CACHE_PERMANENT, '<>')
			->condition('expire', REQUEST_TIME, '<=')
			->execute();
	}

	/**
	 * Check wether there is data in the cache
	 *
	 * @return true iif there is no valid data in the cache
	 */
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