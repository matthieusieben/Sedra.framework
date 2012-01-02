<?php

/**
 * Adds a css stylesheet to the page
 *
 * @param string $media 
 * @return void
 */
function jquery_html_head() {
	Load::helper('html');

	if(DEVEL) {
		js(stream_resolve_include_path('jquery.js'));
	} else {
		js('http://code.jquery.com/jquery-latest.min.js');
	}
}
