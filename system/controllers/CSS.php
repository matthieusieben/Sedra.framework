<?php

load::library('LessPHP');

/**
 * Less CSS compiler controller.
 *
 * @author	Matthieu Sieben
 * @todo	Send mime/type headers
 */
class CSS extends Controller {
	# The cache file only depends on the url
	public $cache_flags = CACHE_LEVEL_URL;

	public function _generate() {
		# Get the css/less file path
		$file_path = BASE_DIR . Url::$query_string;

		# Compile the less file
		$lessc = new lessc($file_path);

		# Return the parsed result
		return $lessc->parse();
	}
}

?>
