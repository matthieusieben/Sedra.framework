<?php

require_once 'user.php';
require_once 'database.php';

session_set_save_handler(
	'_session_open',
	'_session_close',
	'_session_read',
	'_session_write',
	'_session_destroy',
	'_session_garbage_collection'
);

session_start();

global $user;

if(!isset($user)) {
	throw new FrameworkException(t('No user data'), 500, E_ERROR);
}

user_setup_environment();

function _session_open() {
	return TRUE;
}

function _session_close() {
	return TRUE;
}

function _session_read($sid) {
	global $user;

	// Make sure the session data is written
	hook_register('shutdown', 'session_write_close');

	if (!isset($_COOKIE[session_name()])) {
		$user = anonymous_user();
		return '';
	}

	$user = db_query("SELECT u.*, s.* FROM {users} u INNER JOIN {sessions} s ON u.uid = s.uid WHERE s.sid = :sid", array(':sid' => $sid))->fetchObject('User');

	// We found the client's session record and they are an authenticated,
	// active user.
	if ($user && (int) $user->uid > 0 && (int) $user->status == 1) {
		// This is done to unserialize the data member of $user.
		$user->data = @unserialize($user->data);
	}
	else if ($user) {
		// The user is anonymous or blocked. Only preserve two fields from the
		// {sessions} table.
		$account = anonymous_user();
		$account->sid = $sid;
		$account->session = $user->session;
		$account->timestamp = $user->timestamp;
		$user = $account;
	}
	else {
		// The session has expired.
		$user = anonymous_user();
		$user->sid = $sid;
		$user->session = '';
	}

	reg('session_last_read', array(
		'sid' => $sid,
		'value' => $user->session,
	));

	return $user->session;
}

function _session_write($sid, $value) {
	global $user;

	try {
		# We may have had an error connecting to the database
		if(!isset($user)) {
			return TRUE;
		}

		if ($user->uid && REQUEST_TIME - $user->access > config('session.write_interval', 180)) {
			$user->access = REQUEST_TIME;
		}

		$user->save();

		if (!$user->uid && empty($_COOKIE[session_name()]) && empty($value)) {
			return TRUE;
		}

		$last_read = reg('session_last_read');
		$is_changed = $last_read['sid'] != $sid || $last_read['value'] !== $value;

		if ($is_changed || !isset($user->timestamp) || REQUEST_TIME - $user->timestamp > config('session.write_interval', 180)) {
			db_merge('sessions')
				->key(array(
					'sid' => $sid
				))
				->fields(array(
					'uid' => $user->uid,
					'hostname' => ip_address(),
					'session' => $value,
					'timestamp' => REQUEST_TIME,
				))
				->execute();
		}

		return TRUE;
	}
	catch (Exception $exception) {
		// TODO : print an error message
		return FALSE;
	}
}

function _session_garbage_collection($lifetime) {
	db_delete('sessions')
		->condition('timestamp', REQUEST_TIME - $lifetime, '<')
		->execute();
	return TRUE;
}

function _session_destroy($sid) {
	global $user;

	// Delete session data.
	db_delete('sessions')
		->condition('sid', $sid)
		->execute();

	$_SESSION = array();
	$user = anonymous_user();

	if (isset($_COOKIE[session_name()])) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', REQUEST_TIME - 86400, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		unset($_COOKIE[session_name()]);
	}
}

function session_regenerate() {
	$old_session_id = session_id();
	session_id(hash_base64(uniqid(mt_rand(), TRUE) . random_bytes(55)));

	$params = session_get_cookie_params();
	$expire = $params['lifetime'] ? REQUEST_TIME + $params['lifetime'] : 0;
	setcookie(session_name(), session_id(), $expire, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	db_update('sessions')
		->fields(array('sid' => session_id()))
		->condition('sid', $old_session_id)
		->execute();

	user_setup_environment();
}

function session_destroy_uid($uid) {
	db_delete('sessions')
		->condition('uid', $uid)
		->execute();
}
