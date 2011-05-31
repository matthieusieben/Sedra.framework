<?php

/**
 * Attach (or remove) multiple callbacks to an event and trigger those callbacks when that event is called.
 */
class Hook {
	
	public static $hooks = array();
	
	public static function register($step, $callback, $unregister_others = FALSE)
	{
		if($callback === FALSE) {
			unset(self::$hooks[$step]);
		}
		elseif($unregister_others) {
			self::$hooks[$step] = array($callback);
		}
		else {
			self::$hooks[$step][] = $callback;
		}
	}

	public static function call($step, $arg = NULL)
	{
		if(self::registered($step)) {
			foreach(self::$hooks[$step] as $h) {
				if(is_callable($h)) {
					$arg = call_user_func($h, $arg);
				}
			}
		}
		return $arg;
	}

	public static function registered($step)
	{
		return !empty(self::$hooks[$step]);
	}
}