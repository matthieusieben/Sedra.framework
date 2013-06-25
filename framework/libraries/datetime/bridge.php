<?php

# http://tarruda.github.io/bootstrap-datetimepicker/

load_library('bootstrap');

hook_register('html_head', function () {
	echo theme_css('libraries/datetime/css/bootstrap-datetimepicker.min.css');
});

hook_register('html_foot', function () {
	global $language;
	echo theme_js('libraries/datetime/js/bootstrap-datetimepicker.min.js');
	echo theme_js('libraries/datetime/js/locales/bootstrap-datetimepicker.'.$language.'.js');
	echo '<script>$("input[type=datetime]").parent().datetimepicker({language: "'.$language.'"});</script>';
});
