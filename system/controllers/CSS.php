<?php

load::library('LessPHP');

/**
 * Less CSS compiler controller.
 *
 * @author	Matthieu Sieben
 * @todo	Send headers & cache output (if not DEVEL)
 */
class CSS extends Controller {

	public $cache_flags = CACHE_LEVEL_URL;

	public function __call($fun, $args) {
		try {
			# Get the css/less file path
			$file_path = BASE_DIR . Url::$query_string;

			# Compile the less file
			$lessc = new lessc($file_path);

			# Return the parsed result
			return $lessc->parse();
		} catch (Exception $e) {
			# Print a "page does not exists" message on error
			throw new Sedra404Exception();
		}
	}
}

?>
