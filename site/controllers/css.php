<?php
/**
 * Less CSS compiler controller.
 *
 * @author	Matthieu Sieben
 * @todo	Send headers & cache output (if not DEVEL)
 */

// Load the compiller library
Load::library('LessPHP');

// Get the less file path
$file_path = BASE_DIR . Url::$query_string;

try {
	// Compile the less file
	$lessc = new lessc($file_path);
	// Print the parsed result
	echo $lessc->parse();
	
	if(DEVEL) {
		echo '/* Generated in '.round((microtime() - START_TIME) * 1000).' ms */';
	}
} catch (Exception $e) {
	// Print a "page does not exists" message on error
	throw new Sedra404Exception();
}