<?php

# Start database debugging
if(class_exists('Database'))
	Database::startLog('devel');

# Load php-ref
require_once 'libraries/php-ref/ref.php';

# Configure PHP-ref
ref::config('shortcutFunc', array('r', 'rt', 'dvm', 'devel'));
ref::config('maxDepth', 0);
ref::config('showMethods', TRUE);
ref::config('showPrivateMembers', TRUE);
ref::config('showResourceInfo', TRUE);

# Get default css and js files
reg('library_ref_css', ref::config('stylePath'));
reg('library_ref_js', ref::config('scriptPath'));

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
hook_register('html_head', function () {
	echo theme_css(str_replace('{:dir}', 'libraries/php-ref', reg('library_ref_css')));
	echo theme_js(str_replace('{:dir}', 'libraries/php-ref', reg('library_ref_js')));
});

# Devel block
hook_register('html_foot', function () {
	echo '<div id="devel_block" class="container">';

	$load_time = round((microtime(TRUE) - START_TIME) * 1000, 2) . ' ms';
	devel($load_time);

	global $user;
	if(isset($_GET['user']))
		devel($user);

	if(isset($_GET['queries']))
		if(class_exists('Database'))
			if($queries = @Database::getLog('devel', 'default'))
				devel($queries);

	if(isset($_GET['globals']))
		devel($GLOBALS);

	echo '</div>';
});

return TRUE;
