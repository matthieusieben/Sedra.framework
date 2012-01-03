<?php

/**
 * 
 */
class Theme {
	# TODO
	public static $theme = 'default';
	
	/**
	 * Loads a view files and returns the generated content as a string.
	 *
	 * @param string $name	The name of the view to load (eg 'admin/my_view')
	 * @param string $data	An array containing variables to be extracted
	 * @return string	The generated content from the view
	 * @throws SedraLoadException if the view file cannot be found
	 */
	public static function view( $name, $data = array() ) {
		$data = (array) $data + array(
			'site_name' => 'Sedra',
		); # TODO : from cms
		return Load::view(self::view_path($name), $data);
	}

	public static function css( $file )
	{
		return Url::file(self::css_path($file));
	}

	public static function js( $file )
	{
		return Url::file(self::js_path($file));
	}

	public static function view_path( $name ) {
		# Get the file path from theme folders
		$file = self::theme_dir().$name.'.php';
		# If not found in theme folder, look into the views folder
		if(!is_readable($file)) $file = stream_resolve_include_path('views/'.$name.'.php');
		# Throw an exception if the file could not be found
		if(!is_readable($file)) throw new SedraLoadException( 'view', $name );
		return $file;
	}

	public static function css_path($file = NULL) {
		return self::theme_dir().'/css/'.$file;
	}

	public static function js_path($file = NULL) {
		return self::theme_dir().'/js/'.$file;
	}

	public static function theme_dir() {
		return SITE_DIR.'themes/'.self::$theme .'/';
	}

	public static function get_info($theme = NULL)
	{
		$info = NULL;
		$info_file = $theme ?
			SITE_DIR."themes/${theme}/info.php" :
			self::theme_dir() ;
		if(file_exists($info_file)) {
			$info = include($info_file);
		}
		return (array) $info;
	}

	public static function get_block_placeholders($theme = NULL) {
		$info = self::get_info($theme);
		return (array) val($info['block_placeholders']);
	}
}
