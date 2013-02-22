<?php

require_once __DIR__.'/php-ref/ref.php';

ref::config('shortcutFunc', array('r', 'rt', 'kprintr', 'dvm', 'devel'));
ref::config('expandDepth', 0);

ref::config('stylePath', FALSE);
ref::config('scriptPath', FALSE);

hook_register('html_foot', function () {
	echo theme_css('framework/libraries/ref/php-ref/ref.css');
	echo theme_js('framework/libraries/ref/php-ref/ref.js');
});
