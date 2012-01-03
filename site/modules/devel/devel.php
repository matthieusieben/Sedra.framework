<?php

Load::helper('devel');

/**
 * Add the main controller to the devel pannel
 *
 * @return void
 */
function devel_main_controller_loaded() {
	global $controller;

	# Dump the main controller
	devel($controller, 'Main controller');
}

/**
 * Calls include_source('views/devel.php'); at the very end of the <body> tag.
 *
 * @return void
 */
function devel_html_body_end() {
	# Look for the themed devel.php file and prints its source
	include_source(Theme::view_path('devel'));
}

/**
 * Adds a css stylesheet to the page
 *
 * @param string $media 
 * @return void
 */
function devel_html_head() {
	Load::helper('html');
	css(stream_resolve_include_path('devel.css'));
	js(stream_resolve_include_path('devel.js'));
}

/**
 * Start logging the database
 *
 * @param string $library 
 * @return void
 */
function devel_library_loaded($library) {
	if(strToLower($library) === 'database') {
		Database::startLog('DEVEL');
	}
}
