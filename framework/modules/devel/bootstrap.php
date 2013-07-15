<?php

require_once 'includes/database.php';
Database::startLog('devel');

# Load PHP-ref
require_once 'modules/devel/php-ref/ref.php';

global $_library_ref_css, $_library_ref_js;

# Configure PHP-ref
ref::config('shortcutFunc', array('r', 'rt', 'dvm', 'devel'));
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

function devel($variable) {
	return r($variable);
}

function dvm($variable) {

	ob_start();
	r($variable);
	$devel = ob_get_clean();

	require_once 'includes/message.php';
	return message(MESSAGE_WARNING, $devel);
}

# Include JS and CSS at end of page
hook_register('html_foot', function () {
	global $_library_ref_css, $_library_ref_js;

	echo theme_css(str_replace('{:dir}', 'modules/devel/php-ref', $_library_ref_css));
	echo theme_js(str_replace('{:dir}', 'modules/devel/php-ref', $_library_ref_js));
});

# Devel block
hook_register('html_foot', function () {
	echo '<div id="devel_block" class="container">';

	$load_time = round((microtime(TRUE) - START_TIME) * 1000, 2) . ' ms';
	devel($load_time);

	global $user;
	if(isset($user)) devel($user);

	if($queries = @Database::getLog('devel', 'default'))
		devel($queries);

	devel($GLOBALS);

	echo '</div>';
});


return TRUE;
