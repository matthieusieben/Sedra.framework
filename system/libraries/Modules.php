<?php

/**
 * 
 */
class Modules {
	
	public static $config;
	
	public static function config($module, $key, $default = NULL) {
		return val(self::$config[$module][$key], $default);
	}

	public static function loaded() {
		# TODO : from database
		return (array) config('modules');
	}
}

