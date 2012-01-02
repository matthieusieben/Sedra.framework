<?php

/**
 * Class loader
 */
class Load
{
	/*
	 * -------------------------------------------------------------------------
	 * File loading functions
	 * -------------------------------------------------------------------------
	 */

	public static function module( $name )
	{
		# Folder in which the module resides
		$module_dir = SITE_DIR.'modules/'.$name.'/';

		# Setup new path
		$current_path = get_include_path();
		$new_path = './' .PATH_SEPARATOR. $module_dir .PATH_SEPARATOR. $current_path;
		set_include_path($new_path);

		if( !include_module( 'modules', $name, TRUE ) ) {
			# Reset path if the module was not loaded
			set_include_path($old_path);
			throw new SedraLoadException( 'module', $name );
		}

		# Load the language file
		Lang::register_folder( $module_dir . '/languages/' );

		# Load the hooks from the new module
		Hook::load($name);

		# Notify modules that a new module was loaded
		Hook::call('module_loaded', $name);
	}

	/**
	 * Tries to load a site controller (first) or a system controller
	 * (otherwise) retruns a controller object if it could be loaded and is
	 * a subclass of 'Controller'.
	 *
	 * @param string $name	The controller to load
	 * @return bool
	 */
	public static function controller( $name, $arg = array() )
	{
		if( !include_module('controllers', $name)) {
			throw new SedraLoadException('controller', $name );
		}

		if( !class_exists($name, FALSE) || !is_subclass_of($name, 'Controller')) {
			throw new SedraLoadException('controller', $name );
		}

		$controller = new $name( $arg );

		return Hook::call('controller_loaded', $controller);
	}

	public static function library( $name )
	{
		if( !include_module( 'libraries', $name ) )
		{
			throw new SedraLoadException( 'library', $name );
		}

		Hook::call('library_loaded', $name);
	}

	public static function model( $name )
	{
		if( !include_module( 'models', $name ) )
		{
			throw new SedraLoadException( 'model', $name );
		}

		Hook::call('model_loaded', $name);
	}

	public static function helper( $name )
	{
		if( !include_module( 'helpers', $name ) )
		{
			throw new SedraLoadException( 'helper', $name );
		}

		Hook::call('helper_loaded', $name);
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
		$__file = Hook::call('load_view_file', $__file);
		$__data = Hook::call('load_view_data', $__data);

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
		return Hook::call('view_output', ob_get_clean());
	}

	/*
	 * -------------------------------------------------------------------------
	 * Shortcut functions
	 * -------------------------------------------------------------------------
	 */

	public static function db()
	{
		Load::library('database');
	}
	
	public static function user()
	{
		Load::library('session');
	}
}
