<?php

load::library('LessPHP');

/**
 * Less CSS compiler controller.
 *
 * @author	Matthieu Sieben
 * @todo	Send mime/type headers
 */
class Less extends Controller {
	# The cache file only depends on the url
	public $cache_flags = CACHE_LEVEL_URL;

	public function _generate() {
		try {
			# The .htaccess file adds "less/" to trigger this controller.
			$relative_path = substr(Url::$query_string, 5);
			
			# Get the css/less file path
			$file_path = BASE_DIR . $relative_path;

			# Compile the less file
			$lessc = new lessc($file_path);

			# Set the content to the parsed result
			return $this->content = $lessc->parse();
		} catch(Exception $e) {
			throw new SedraException($e);
		}
	}
}

?>
