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

	/**
	 * Tries to load a system controller (first) or a module controller
	 * (otherwise) retruns true iif the controller could be loaded and is
	 * a subclass of 'Controller'.
	 *
	 * @param string $name	The controller to load
	 * @return bool
	 */
	public static function controller( $name )
	{
		if(	!include_module('controllers', $name) )
		{
			throw new Sedra404Exception();
		}
	}

	public static function library( $name )
	{
		if( !include_module( 'libraries', $name ) )
		{
			throw new LoadException( 'library', $name );
		}
	}

	public static function model( $name )
	{
		if( !include_module( 'models', $name ) )
		{
			throw new LoadException( 'model', $name );
		}
	}

	public static function helper( $name )
	{
		if( !include_module( 'helpers', $name ) )
		{
			throw new LoadException( 'helper', $name );
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
		// Get the file path
		$__file = stream_resolve_include_path("views/$__name.php");

		// Throw an exception if the file could not be found
		if(!$__file) throw new LoadException( 'view', $__name );
		
		// Buffer the output
		ob_start();

		// Excract the varialbes in the current scope
		extract($__data);

		// Include the file
		require $__file;

		// Return the buffered output
		return ob_get_clean();
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
