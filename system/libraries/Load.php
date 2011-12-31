<?php

/**
 * Class loader
 */
class Load
{
	public static function auto()
	{
		$libraries = (array) config('autoload/libaries');
		$models = (array) config('autoload/models');
		$helpers = (array) config('autoload/helpers');
		$hooks = (array) config('autoload/hooks');

		foreach($libraries as $library) {
			Load::library($library);
		}

		foreach($models as $model) {
			Load::model($model);
		}

		foreach($helpers as $helper) {
			Load::helper($helper);
		}

		foreach($hooks as $hook) {
			Load::hook($hook);
		}
	}

	/*
	 * -------------------------------------------------------------------------
	 * File loading functions
	 * -------------------------------------------------------------------------
	 */

	/**
	 * Tries to load a site controller (first) or a system controller
	 * (otherwise) retruns a controller object if it could be loaded and is
	 * a subclass of 'Controller'.
	 *
	 * @param string $name	The controller to load
	 * @return bool
	 */
	public static function controller( $class, $arg = array() )
	{
		if( !include_module('controllers', $class)) {
			throw new SedraLoadException('controller', $class );
		}

		if( !class_exists($class, FALSE) || !is_subclass_of($class, 'Controller')) {
			throw new SedraLoadException('controller', $class );
		}

		return new $class( $arg );
	}

	public static function library( $class )
	{
		if( !include_module( 'libraries', $class ) )
		{
			throw new SedraLoadException( 'library', $class );
		}
	}

	public static function model( $class )
	{
		if( !include_module( 'models', $class ) )
		{
			throw new SedraLoadException( 'model', $class );
		}
	}

	public static function helper( $file )
	{
		if( !include_module( 'helpers', $file ) )
		{
			throw new SedraLoadException( 'helper', $file );
		}
	}

	public static function hook( $file )
	{
		if( !include_module( 'hooks', $file ) )
		{
			throw new SedraLoadException( 'hook', $file );
		}
	}

	public static function lang( $folder )
	{
		Lang::register_folder( $folder );
	}

	/**
	 * Loads a view files and returns the generated content as a string.
	 *
	 * @param string $__file	The view file to load
	 * @param string $__data	An array containing variables to be extracted
	 * @return string	The generated content from the view
	 * @throws SedraLoadException if the view file cannot be found
	 */
	public static function view( $__file, $__data = array() )
	{
		# Alter parameters by hooks
		$__file = Hook::call(HOOK_LOAD_VIEW_FILE, $__file);
		$__data = Hook::call(HOOK_LOAD_VIEW_DATA, $__data);

		# Tell whether the file exists and is readable
		if(!is_readable($__file)) {
			throw new SedraLoadException( 'view', $__file );
		}

		# Buffer the output
		ob_start();

		# Excract the varialbes in the current scope
		extract($__data);

		# Include the file
		require $__file;

		# Return the buffered output
		return Hook::call(HOOK_LOAD_VIEW_OUTPUT, ob_get_clean());
	}

	/*
	 * -------------------------------------------------------------------------
	 * Shortcut functions
	 * -------------------------------------------------------------------------
	 */

	public static function db()
	{
		Load::library('Database');
		
		if(DEVEL) {
			Database::startLog('DEVEL');
		}
	}
	
	public static function user()
	{
		Load::library('Session');
	}
}
