<?php

define('LESS_CONTROLLER_COMMENT', '/* generated in <?php echo round((microtime() - START_TIME) * 1000); ?> ms */');

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
			# Load the less compiler class
			load::library('lessphp');

			# Remove "less/" from the url
			$relative_path = substr(Url::$query_string, 5);
			
			# Get the less file path
			$file_path = Theme::css_path($relative_path);

			# Compile the less file
			$lessc = new lessc($file_path);

			# Set the content to the parsed result
			return $this->content = $lessc->parse() . LESS_CONTROLLER_COMMENT;
		} catch(Exception $e) {
			throw new SedraException($e);
		}
	}
}

?>
