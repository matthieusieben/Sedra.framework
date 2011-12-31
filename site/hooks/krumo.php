<?php

# Hook function
function krumo_dump_hook($_, $variable) {
	# Include the lib only if needed
	Load::library('krumo');

	# print the dumped varialbe
	krumo($variable);

	# The data is not altered by this hook
	return $variable;
}

# Setup krumo as dumper
Hook::register(HOOK_DUMP, 'krumo_dump_hook');
