<?php

# Calls include_source('views/devel.php'); at the very end of the <body> tag.
Hook::register(HOOK_HTML_BODY_END, 'devel_html_body_end');

# Adds a css stylesheet to the page
Hook::register(HOOK_HTML_HEAD, 'devel_html_head');

# Start loggging the database
Hook::register(HOOK_LOAD_LIBRARY_LOADED, 'devel_library_loaded');

# Show the main controller
Hook::register(HOOK_MAIN_CONTROLLER_LOADED, 'devel_main_controller_loaded');

function devel_main_controller_loaded() {
	global $controller;

	# Dump the main controller
	devel($controller, 'Main controller');
}

function devel_html_body_end() {
	# Look for the themed devel.php file and prints its source
	include_source(Theme::view_path('devel'));
}

function devel_html_head($media = 'screen') {
	$href = Url::file(stream_resolve_include_path('views/devel.css'));
	if($href) {
		echo '<link rel="stylesheet" type="text/css" href="'.$href.'" media="'.$media.'" />';
	}
}

function devel_library_loaded($_, $library) {
	if(strToLower($library) === 'database') {
		Database::startLog('DEVEL');
	}
	return $library;
}
