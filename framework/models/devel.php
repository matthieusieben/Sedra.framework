<?php

load_model('database');
Database::startLog('devel');

function devel($variable) {
	load_model('kprintr');
	return kprintr($variable);
}

function dvm($variable) {
	load_model('message');
	load_model('kprintr');
	return message(MESSAGE_WARNING, kprintr($variable, TRUE));
}

hook_register('html_foot', function () {
	load_model('kprintr');
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
