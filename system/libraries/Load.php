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

	public static function hook( $filename )
	{
		if( !include_module( 'hooks', $filename ) )
		{
			throw new SedraLoadException( 'hook', $class );
		}
	}

	public static function lang( $folder )
	{
		Lang::register_folder( $folder );
	}

	/**
	 * Loads a view files and retruns the generated content as a string.
	 *
	 * @param string $name	The name of the view to load (eg 'admin/my_view')
	 * @param string $data	An array containing variables to be extracted
	 * @return string	The generated content from the view
	 * @throws SedraLoadException if the view file cannot be found
	 */
	public static function view( $__name = 'index', $__data = array() )
	{
		# Alter parameters by hooks
		$__name = Hook::call(HOOK_LOAD_VIEW_NAME, $__name);
		$__data = Hook::call(HOOK_LOAD_VIEW_DATA, $__data);


		# Get the file path
		$__file = self::view_path( $__name );
		
		# Buffer the output
		ob_start();

		# Excract the varialbes in the current scope
		extract($__data);

		# Include the file
		require $__file;

		# Return the buffered output
		return Hook::call(HOOK_LOAD_VIEW_OUTPUT, ob_get_clean());
	}

	/**
	 * Get the full path to a view file inside the include path.
	 *
	 * @param string $name The name of the view to load
	 * @return The file name if it exists
	 * @throws SedraLoadException if the file cannot be found
	 */
	public static function view_path($name) {
		# Get the file path from different folders
		$file = stream_resolve_include_path("$name.php");
		if(!$file) $file = stream_resolve_include_path("views/$name.php");
		# Throw an exception if the file could not be found
		if(!$file) throw new SedraLoadException( 'view', $name );
		return $file;
	}

	/**
	 * Add a folder to the view path
	 *
	 * @param string $folder a folder within SITE_DIR
	 * @return void
	 * @see SITE_DIR
	 */
	public static function add_view_path($folder) {
		if(is_dir(SITE_DIR.$folder)) {
			set_include_path(SITE_DIR.$folder .PATH_SEPARATOR. get_include_path());
		}
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
