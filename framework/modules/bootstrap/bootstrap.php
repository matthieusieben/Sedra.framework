<?php

if(!load_module('jquery'))
	return FALSE;

hook_register('html_head', function () {
	echo theme_css('modules/bootstrap/css/bootstrap.min.css');
	echo theme_css('modules/bootstrap/css/bootstrap-responsive.min.css');
});

hook_register('html_foot', function () {
	echo theme_js('modules/bootstrap/js/bootstrap.min.js');
});
