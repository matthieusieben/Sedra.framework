<?php

require_once 'database.php';

function watchdog_notify($id = NULL, $timeout = NULL) {
	if(!$timeout)
		$timeout = config('watchdog.timeout', 3600);

	# Remove old values
	db_delete('watchdog')
		->condition('timestamp', REQUEST_TIME, '<')
		->execute();

	# Update count
	db_merge('watchdog')
		->fields(array(
			'timestamp' => REQUEST_TIME + $timeout,
		))
		->expression('count', 'count + 1')
		->key(array(
			'id' => $id,
			'hostname' => ip_address(),
		))
		->execute();
}

function watchdog_suspect($id = NULL, $attempts = NULL) {
	if(!$attempts)
		$attempts = config('watchdog.attempts', 3);
	$count = watchdog_getcount($id);
	return $count > $attempts;
}

function watchdog_getcount($id = NULL) {
	$r = db_select('watchdog', 'w')
		->fields('w', array('count'))
		->condition('id', $id)
		->condition('hostname', ip_address())
		->condition('timestamp', REQUEST_TIME, '>')
		->execute()
		->fetch();

	return $r ? $r->count : 0;
}

function watchdog_release($id = NULL) {
	db_delete('watchdog')
		->condition('id', $id)
		->condition('hostname', ip_address())
		->execute();
}

function _form_handle_watchdog(&$form, &$field) {
	load_model('captcha');

	$field += array(
		'timeout' => NULL,
		'attempts' => NULL,
		'release' => FALSE,
	);

	if($field['release']) {
		$form['_watchdog_release'] = TRUE;
	}

	if($form['submitted']) {
		watchdog_notify($form['id'], $field['timeout']);
	}

	if(watchdog_suspect($form['id'], $field['attempts'])) {
		$field['_watchdog'] = TRUE;
		return _form_handle_captcha($form, $field);
	}

	$field['_watchdog'] = FALSE;
	$field['view'] = NULL;
}

function _form_callback_watchdog(&$form, &$field) {
	if($field['_watchdog'])
		return _form_callback_captcha($form, $field);
	else
		return TRUE;
}

hook_register('form_run', function(&$form) {
	if(@$form['_watchdog_release']) {
		if($form['valid']) {
			watchdog_release($form['id']);
		}
	}
});
