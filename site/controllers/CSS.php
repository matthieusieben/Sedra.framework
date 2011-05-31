<?php

/**
 * Less CSS compiler controller.
 *
 * @author	Matthieu Sieben
 * @todo	Send headers & cache output (if not DEVEL)
 */
class CSS extends Controller {

	public function __construct($args = NULL) {
		parent::__construct($args);
		# Library loading
		load::library('LessPHP');
	}

	public function index() {
		throw new Sedra404Exception();
	}
	
	public function __call($fun, $args) {

		# Get the less file path
		$file_path = BASE_DIR . Url::$query_string;

		try {
			# Compile the less file
			$lessc = new lessc($file_path);

			# Print the parsed result
			$this->content = $lessc->parse();

			if(DEVEL) {
				$time = round((microtime() - START_TIME) * 1000);
				$this->content .= "/* CSS file generated from less code in {$time} ms */";
			}

		} catch (Exception $e) {
			# Print a "page does not exists" message on error
			throw new Sedra404Exception();
		}
	}
	
	public function in_cache() {
		return FALSE;
	}

	public function cache() {
		return FALSE;
	}
}

?>
