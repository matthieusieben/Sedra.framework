<?php

# Increase string width
define('KRUMO_TRUNCATE_LENGTH', 100);

# Hook function
function krumo_hook($_, $variable) {
	# Include the lib only if needed
	require_once 'krumo/class.krumo.php';

	# print the dumped varialbe
	krumo($variable);

	# The data is not altered by this hook
	return $variable;
}

# Unregister any other dumper and setup krumo as one
Hook::register(HOOK_DUMP, 'krumo_hook');
