<?php

if (DEVEL) {
	require_once 'database.php';
	require_once 'kprintr.php';

	Database::startLog('devel');

	function devel($variable) {
		kprintr($variable);
	}

	function dvm($variable) {
		message(MESSAGE_WARNING, kprintr($variable, TRUE));
	}

	hook_register('html_foot', function () {
		echo '<div id="devel_block" class="container">';

		$load_time = round((microtime(TRUE) - START_TIME) * 1000, 2) . ' ms';
		kprintr($load_time);

		global $user;
		if(isset($user)) kprintr($user);

		if($queries = @Database::getLog('devel', 'default'))
			kprintr($queries);

		kprintr($GLOBALS);

		echo '</div>';
	});
}
else {
	function devel($variable) {}
	function dvm($variable) {}
}