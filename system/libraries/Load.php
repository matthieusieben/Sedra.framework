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

		foreach($libraries as $library) {
			Load::library($library);
		}

		foreach($models as $model) {
			Load::model($model);
		}

		foreach($helpers as $helper) {
			Load::helper($helper);
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
	public static function controller( $class, $arg = NULL )
	{
		if( !include_module('controllers', $class)) {
			throw new Sedra404Exception();
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

	public static function lang_file( $file )
	{
		if( !include_module( 'helpers', $file ) )
		{
			throw new SedraLoadException( 'helper', $file );
		}
	}

	/**
	 * Loads a view files and retruns the generated content as a string.
	 *
	 * @param string $name	The name of the view to load (eg 'admin/my_view')
	 * @param string $data	An array containing variables to be extracted
	 * @return string	The generated content from the view
	 */
	public static function view( $__name = 'index', $__data = array() )
	{
		# Alter parameters by hooks
		$__name = Hook::call(HOOK_VIEW_NAME, $__name);
		$__data = Hook::call(HOOK_VIEW_DATA, $__data);

		# Get the file path
		$__file = stream_resolve_include_path("views/$__name.php");

		# Throw an exception if the file could not be found
		if(!$__file) throw new SedraLoadException( 'view', $__name );
		
		# Buffer the output
		ob_start();

		# Excract the varialbes in the current scope
		extract($__data);

		# Include the file
		require $__file;

		# Return the buffered output
		return Hook::call(HOOK_VIEW_OUTPUT, ob_get_clean());
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
