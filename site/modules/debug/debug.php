<?php

function hook_print_debug_variables() {
	# Look for the themed debug.php file and prints its source
	include_source(Theme::view_path('debug'));
}

# Calls include_source('views/debug.php'); at the very end of the <body> tag.
Hook::register(HOOK_HTML_BODY_END, 'hook_print_debug_variables');
