<?php

global $_library_ref_css, $_library_ref_js;

require_once __DIR__.'/php-ref/ref.php';

ref::config('shortcutFunc', array('r', 'rt', 'kprintr', 'dvm', 'devel'));
ref::config('maxDepth', 0);
ref::config('showMethods', TRUE);
ref::config('showPrivateMembers', TRUE);
ref::config('showResourceInfo', TRUE);

# Get default css and js files
$_library_ref_css = ref::config('stylePath', NULL);
$_library_ref_js = ref::config('scriptPath', NULL);

# Disable JS and CSS printing
ref::config('stylePath', FALSE);
ref::config('scriptPath', FALSE);

# Include JS and CSS at end of page
hook_register('html_foot', function () {
	global $_library_ref_css, $_library_ref_js;

	echo theme_css(str_replace('{:dir}', 'libraries/ref/php-ref', $_library_ref_css));
	echo theme_js(str_replace('{:dir}', 'libraries/ref/php-ref', $_library_ref_js));
});
