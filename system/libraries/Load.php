<?php

/**
 * Class loader
 */
class Load
{
	public static function auto()
	{
		$modules = (array) config('autoload/modules');

		foreach($modules as $module) {
			Load::module($module);
		}
	}

	/*
	 * -------------------------------------------------------------------------
	 * File loading functions
	 * -------------------------------------------------------------------------
	 */

	public static function module( $name )
	{
		if( !include_module( 'modules', $name, TRUE ) )
		{
			throw new SedraLoadException( 'module', $name );
		}

		$module_dir = SITE_DIR.'modules/'.$name.'/';

		set_include_path($module_dir .PATH_SEPARATOR. get_include_path());

		Lang::register_folder( $module_dir . '/languages/' );
	}

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

	public static function helper( $name )
	{
		if( !include_module( 'helpers', $name ) )
		{
			throw new SedraLoadException( 'helper', $name );
		}
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
