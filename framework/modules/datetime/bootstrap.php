<?php

# http://tarruda.github.io/bootstrap-datetimepicker/

if(!load_module('bootstrap'))
	return FALSE;

hook_register('html_head', function () {
	echo theme_css('modules/datetime/css/bootstrap-datetimepicker.min.css');
});

hook_register('html_foot', function () {
	global $language;
	echo theme_js('modules/datetime/js/bootstrap-datetimepicker.min.js');
	echo theme_js('modules/datetime/js/locales/bootstrap-datetimepicker.'.$language.'.js');
	echo '<script>$("input[type=datetime]").parent().datetimepicker({language: "'.$language.'"});</script>';
});
