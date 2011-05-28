<?php

/**
 * Class loader
 */
class Load
{

	/*
	 * -------------------------------------------------------------------------
	 * File & Class loading functions
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
		if(	!self::load_file('controllers', $name) )
		{
			throw new Sedra404Exception();
		}
	}

	public static function library( $name )
	{
		if( !self::load_file( 'libraries', $name ) )
		{
			throw new LoadException( 'library', $name );
		}
	}

	public static function model( $name )
	{
		if( !self::load_file( 'models', $name ) )
		{
			throw new LoadException( 'model', $name );
		}
	}

	public static function helper( $name )
	{
		if( !self::load_file( 'helpers', $name ) )
		{
			throw new LoadException( 'helper', $name );
		}
	}

	/**
	 * Include a system file only once.
	 *
	 * @param string $dir		The subfolder in which the file cn be found
	 * @param string $file_name	The file to load
	 * @param string $module	The module in which the file resides
	 * @return TRUE	if the file was (already) loaded
	 */
	private static function load_file( $dir, $file_name )
	{
		if($path = stream_resolve_include_path($dir . DS . $file_name . '.php')) {
			require_once $path;
			return TRUE;
		}
		
		if($path = stream_resolve_include_path($dir . DS . $file_name . DS . $file_name . '.php')) {
			require_once $path;
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Loads a view files and retruns the generated content as a string.
	 *
	 * @param string $name	The name of the view to load (eg 'admin/my_view')
	 * @param string $data	An array containing variables to be extracted
	 * @return string	The generated content from the view
	 */
	public static function view( $name, $data = array() )
	{
		$path = self::get_view_path($name);
		if(!$path) throw new LoadException( 'view', $name );
		return self::view_file( $path, $data );
	}

	/**
	 * Loads a view files and retruns the generated content as a string.
	 *
	 * @pre						The file $__file must exist
	 * @param string $__file	The view file
	 * @param string $_data		An array containing variables to be extracted
	 * @return string			The generated content from the view
	 */
	public static function view_file( $__file, $_data )
	{
		// Exctract variables in current scope
		extract($_data);

		// Buffer the output
		ob_start();
		require $__file;
		$buffer = ob_get_contents();
		ob_end_clean();

		return $buffer;
	}

	/**
	 * Get the file path of a view file.
	 *
	 * @param string $name		The name of the view (eg 'admin/blocks')
	 * @param string $module	The module in whicht the file can be found
	 * @return string The full path to the file if it exists. NULL otherwise.
	 */
	public static function get_view_path( $name )
	{
		$name = str_replace( '/', DS, $name );
		return stream_resolve_include_path('views' .DS. $name . '.php');
	}

	/*
	 * -------------------------------------------------------------------------
	 * Shortcut functions
	 * -------------------------------------------------------------------------
	 */

	public static function db()
	{
		Load::library('database');
		
		if(DEVEL) {
			Database::startLog('DEVEL');
		}
	}

	public static function user()
	{
		return Load::library('session');
	}

	public static function lang()
	{
		return Load::user();
	}
}

class Render {

	public static function site_view($view, $data = array())
	{
		Load::model('site');
		self::view($view, $data + Site::data($data));
	}

	public static function view($view, $data = array())
	{
		echo Load::view($view, $data);
	}

	public static function exception($e)
	{
		set_status_header($e instanceof SedraException ? $e->getCode() : 500);
		Render::site_view('exception', array('e' => $e));
	}
}
