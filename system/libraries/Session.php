<?php

{
	Load::db();

	session_set_save_handler(
		array('Session','open'), // callback $open
		array('Session','close'), // callback $close
		array('Session','read'), // callback $read
		array('Session','write'), // callback $write
		array('Session','destroy_sid'), // callback $destroy
		array('Session','gc') // callback $gc
		);
	session_start();

	if( isset($_POST['user-login'], $_POST['user-name'], $_POST['user-pass']) && !User::connected() ) {
		// login
		User::authenticate($_POST['user-name'], $_POST['user-pass']);
	}
	elseif( (isset($_POST['user-logout']) || isset($_GET['user-logout'])) && User::connected() ) {
		// logout
		User::logout();
	}

	// TODO : REMOVE THESE LINES
	#User::authenticate('admin','admin');
	#User::logout();
	// ENDTODO

	User::$user->load_session();
}

class Session
{
	/************************************************************************/
	/*                        session save handlers                         */
	/************************************************************************/

	public static function open($save_path, $session_name)
	{
		return TRUE;
	}

	public static function close()
	{
		return TRUE;
	}

	public static function read($sid)
	{
		// Write and Close handlers are called after destructing objects since PHP 5.0.5
		// Thus destructors can use sessions but session handler can't use objects.
		// So we are moving session closure before destructing objects.
		register_shutdown_function('session_write_close');

		// Handle the case of first time visitors and clients that don't store cookies (eg. web crawlers).
		if (isset($_COOKIE[session_name()]))
		{
			// If the session is still active, we have a record of the client's session in the database.
			$user_data = db_query("SELECT u.*, s.session FROM {users} u INNER JOIN {sessions} s ON u.uid = s.uid WHERE s.sid = :sid AND s.hostname = :hostname", array(':sid' => $sid, ':hostname' => ip_address()))->fetchAssoc();

			// We found the client's session record and they are an authenticated,
			// active user.
			if ($user_data && $user_data['uid'] > 0 && $user_data['status'] == 1) {
				User::set(new User($user_data));
				return $user_data['session'];
			} elseif (isset($user_data['session'])) {
				User::set(new AnonymousUser);
				return $user_data['session'];
			}
		}

		User::set(new AnonymousUser);
		return '';
	}

	public static function write($sid, $value)
	{
		$user = User::current();
		// If saving of session data is disabled or if the client doesn't have a session,
		// and one isn't being created ($value), do nothing. This keeps crawlers out of
		// the session table. This reduces memory and server load, and gives more useful
		// statistics. We can't eliminate anonymous session table rows without breaking
		// the throttle module and the "Who's Online" block.
		if( $user->uid == 0 && empty($_COOKIE[session_name()]) && empty($value) ) {
			return TRUE;
		}

		$fields = array(
			'uid' => $user->uid,
			'hostname' => ip_address(),
			'session' => $value,
			'timestamp' => REQUEST_TIME,
		);

		$key = array('sid' => $sid);

		db_merge('sessions')
			->key($key)
			->fields($fields)
			->execute();

		// Last access time is updated no more frequently than once every 180 seconds.
		// This reduces contention in the users table.
		if ($user->uid && ((REQUEST_TIME - $user->access) > config('session/write_interval', 180))) {
			db_update('users')
				->fields(array(
					'access' => REQUEST_TIME
				))
				->condition('uid', $user->uid)
				->execute();
		}

		return TRUE;
	}

	/**
	 * Called when an anonymous user becomes authenticated or vice-versa.
	 */
	public static function regenerate()
	{
		$old_session_id = session_id();
		session_regenerate_id();
		$fields = array('sid' => session_id());
		
		db_update('sessions')
			->fields($fields)
			->condition('sid', $old_session_id)
			->execute();
	}

	/**
	 * Called by PHP session handling with the PHP session ID to end a user's session.
	 *
	 * @param	string $sid	The session id
	 */
	public static function destroy_sid($sid)
	{
		db_delete('sessions')
			->condition('sid', $sid)
			->execute();
	}

	/**
	 * End a specific user's session
	 *
	 * @param	string $uid	The user id
	 */
	public static function destroy_uid($uid)
	{
		db_delete('sessions')
			->condition('uid', $uid)
			->execute();
	}

	public static function gc($lifetime)
	{
		db_delete('sessions')
			->condition('timestamp', REQUEST_TIME - $lifetime, '<')
			->execute();
		return TRUE;
	}
}
