<?php

if(!load_module('jquery', FALSE))
	return FALSE;

hook_register('html_head', function () {
	if(config('devel')) {
		echo theme_css('libraries/bootstrap/css/bootstrap.css');
		echo theme_css('libraries/bootstrap/css/bootstrap-responsive.css');
	}
	else {
		echo theme_css('libraries/bootstrap/css/bootstrap.min.css');
		echo theme_css('libraries/bootstrap/css/bootstrap-responsive.min.css');
	}
});

hook_register('html_foot', function () {
	if(config('devel')) {
		echo theme_js('libraries/bootstrap/js/bootstrap.js');
	}
	else {
		echo theme_js('libraries/bootstrap/js/bootstrap.min.js');
	}
});
