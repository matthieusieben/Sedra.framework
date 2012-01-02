<?php

/**
 * Setup krumo as variable dumper
 *
 * @param string $variable 
 * @return void
 */
function krumo_dump($variable) {
	# Include the lib only if needed
	Load::library('krumo');

	# print the dumped varialbe
	krumo($variable);
}
