<?php

# Calls include_source('views/debug.php'); at the very end of the <body> tag.
Hook::register(HOOK_HTML_BODY_END, 'hook_debug_print_variables');

# Adds a css stylesheet to the page
Hook::register(HOOK_HTML_HEAD, 'hook_debug_insert_css');

# Start loggging the database
Hook::register(HOOK_LOAD_LIBRARY_LOADED, 'hook_debug_loaded_library');

# Show the main controller
Hook::register(HOOK_MAIN_CONTROLLER, 'hook_debug_dump_main_controller');

function hook_debug_dump_main_controller() {
	global $controller;

	# Dump the main controller
	debug($controller, 'Main controller');
}

function hook_debug_print_variables() {
	# Look for the themed debug.php file and prints its source
	include_source(Theme::view_path('debug'));
}

function hook_debug_insert_css($media = 'screen') {
	$href = Url::file(stream_resolve_include_path('views/debug.css'));
	if($href) {
		echo '<link rel="stylesheet" type="text/css" href="'.$href.'" media="'.$media.'" />';
	}
}

function hook_debug_loaded_library($_, $library) {
	if(strToLower($library) === 'database') {
		Database::startLog('DEVEL');
	}
	return $library;
}
