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
	$attempts = max(1, (int) $attempts);
	$count = watchdog_getcount($id);
	$hardlimit = config('watchdog.hardlimit');

	if($hardlimit && $count >= $hardlimit)
		throw new FrameworkException(t('A suspect activity has been detected from your IP address <code>@ip</code>. It has been temporarily blocked. Please try again later.', array('@ip' => ip_address())), 403);
	else if($count < $attempts)
		return 0;
	else if($count === $attempts)
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

	return $r ? (int) $r->count : 0;
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
		'release' => FALSE,
		'required' => TRUE,
	);
	$field['view'] = NULL;

	if($form['submitted']) {
		watchdog_notify($form['id'], $field['timeout']);
	}

	$field['_watchdog_status'] = watchdog_suspect($form['id'], $field['attempts']);
	if($field['_watchdog_status'] > 0) {
		require_once 'includes/captcha.php';
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
