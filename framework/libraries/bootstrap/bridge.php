<?php

load_library('jquery');

hook_register('html_head', function () {
	echo theme_css('libraries/bootstrap/css/bootstrap.min.css');
	echo theme_css('libraries/bootstrap/css/bootstrap-responsive.min.css');
});

hook_register('html_foot', function () {
	echo theme_js( 'libraries/bootstrap/js/bootstrap.min.js');
});
