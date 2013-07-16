<?php

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
	if(is_null($attempts))
		$attempts = config('watchdog.attempts', 3);
	$count = watchdog_getcount($id);
	if($count < $attempts)
		return 0;
	if((int) $count === (int) $attempts)
		return 1;
	else
		return 2;
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
	$field += array(
		'timeout' => NULL,
		'attempts' => NULL,
		'release' => TRUE,
	);
	$field['view'] = NULL;

	if($form['submitted']) {
		watchdog_notify($form['id'], $field['timeout']);
	}

	$field['_watchdog_status'] = watchdog_suspect($form['id'], $field['attempts']);
	if($field['_watchdog_status'] > 0) {
		load_module('captcha');
		return _form_handle_captcha($form, $field);
	}
}

function _form_callback_watchdog(&$form, &$field) {
	# watchdog_suspect() returns 1 the first time the visitor is suspected,
	# 2 afterwards.
	if($field['_watchdog_status'] > 1) {
		if(_form_callback_captcha($form, $field)) {
			if($field['release']) {
				watchdog_release($form['id']);
				$field['view'] = NULL;
			}
			return TRUE;
		}
		else {
			return FALSE;
		}
	} else {
		return TRUE;
	}
}
