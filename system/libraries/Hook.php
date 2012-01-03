<?php

if($cache = Cache::get(array('class' => 'Hook'))) {
	Hook::$data = $cache->data;
}

/**
 * Attach (or remove) multiple callbacks to an event and trigger those callbacks when that event is called.
 */
class Hook {

	public static $data = array(
		'modules' => array(),
		'hooks' => array(
			'bootstrap' => array(),
			'shutdown' => array(),
			'dump' => array(),

			'alter_controller' => array(),
			'alter_controller_name' => array(),
			'alter_controller_arguments' => array(),
			'main_controller_loaded' => array(),

			'alter_query_string' => array(),
			'alter_file_url' => array(),

			'module_loaded' => array(),
			'alter_controller_on_load' => array(),
			'library_loaded' => array(),
			'model_loaded' => array(),
			'helper_loaded' => array(),

			'alter_view_file' => array(),
			'alter_view_data' => array(),
			'alter_view_output' => array(),

			'set_controller_content' => array(),
			'set_controller_html' => array(),
			'alter_controller_before_display' => array(),

			'alter_to_cache' => array(),
			'alter_from_cache' => array(),

			'html_head' => array(),
			'html_body_start' => array(),
			'html_body_end' => array(),
		),
	);


	public static function load( $module ) {
		if(!isset(self::$data['modules'][$module])) {
			# Register new events
			$fn = "${module}_hooks";
			self::$data['modules'][$module] = function_exists($fn) ? (array) call_user_func($fn) : array();
			foreach(self::$data['modules'][$module] as $event) {
				if(!isset(self::$data['hooks'][$event])) {
					self::$data['hooks'][$event] = array();
				}
			}

			# 
			foreach(self::$data['hooks'] as $event => $_) {
				self::register($module, $event);
			}

			Cache::set(array('class' => 'Hook'), self::$data, TRUE);
		}
	}

	/**
	 * Attach (or remove) a callbacks to an event.
	 *
	 * @param string $event the event to which attach a callback
	 * @param callback $callback the callback to execute
	 * @param mixed $argument the first argument that will be used upon hook call
	 * @return TRUE iif the callback provided is valid (i.e. callable)
	 */
	public static function register($module, $event)
	{
		$callback = "${module}_${event}";
		if(function_exists($callback) AND !in_array($callback, self::$data['hooks'][$event])) {
			self::$data['hooks'][$event][] = $callback;
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
	public static function call($event, $result = NULL)
	{
		if(self::registered($event)) {
			foreach(self::$data['hooks'][$event] as $callback) {
				call_user_func($callback, $result);
			}
		}
		return $result;
	}

	public static function alter($event, $value)
	{
		if(self::registered($event)) {
			foreach(self::$data['hooks'][$event] as $callback) {
				$value = call_user_func($callback, $value);
			}
		}
		return $value;
	}

	/**
	 * Allows to know if a callback was associated with some event
	 *
	 * @param string $event 
	 * @return TRUE iif a valid callback was associated the the event
	 */
	public static function registered($event)
	{
		return !empty(self::$data['hooks'][$event]);
	}
}
