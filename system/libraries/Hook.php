<?php

/**
 * Attach (or remove) multiple callbacks to an event and trigger those callbacks when that event is called.
 */
class Hook {
	
	public static $hooks = array();
	
	/**
	 * Attach (or remove) a callbacks to an event.
	 *
	 * @param string $event the event to which attach a callback
	 * @param callback $callback the callback to execute
	 * @param mixed $argument the first argument that will be used upon hook call
	 * @return TRUE iif the callback provided is callable
	 */
	public static function register($step, $callback, $argument = NULL)
	{
		if(is_callable($callback)) {
			self::$hooks[$step][] = array(
				'callback' => $callback,
				'argument' => $argument,
			);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Trigger the callback associated with an event.
	 *
	 * @param string $event the event to trigger
	 * @param mixed $result a second argument for the callable function
	 * @return the $result variable altered by every callback
	 */
	public static function call($step, $result = NULL)
	{
		if(self::registered($step)) {
			foreach(self::$hooks[$step] as $hook) {
				$callback = $hook['callback'];
				$argument = $hook['argument'];
				
				$result = call_user_func($callback, $argument, $result);
			}
		}
		return $result;
	}

	/**
	 * Allows to know if a callback was associated with some event
	 *
	 * @param string $event 
	 * @return TRUE iif a valid callback was associated the the event
	 */
	public static function registered($step)
	{
		return !empty(self::$hooks[$step]);
	}
}