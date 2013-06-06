<?php

define('ANONYMOUS_RID', 0);
define('ADMINISTRATOR_RID', 1);
define('MODERATOR_RID', 2);
define('AUTHENTICATED_RID', 3);

require_once 'session.php';
require_once 'message.php';

global $user;

class User {

	public $sid;
	public $hostname;
	public $session;
	public $timestamp;

	public $uid = 0;
	public $data = array();

	private $rid = ANONYMOUS_RID;
	private $name;
	private $pass;
	private $mail;
	private $language;
	private $timezone;
	private $created;
	private $access;
	private $login;
	private $status;

	protected $updated = FALSE;

	public static $editable_fields = array(
		'rid' => TRUE,
		'name' => TRUE,
		'pass' => TRUE,
		'mail' => TRUE,
		'language' => TRUE,
		'timezone' => TRUE,
		'access' => TRUE,
		'login' => TRUE,
		'status' => TRUE,
	);

	public function __get($field) {

		if (empty($this->{$field})) {
			# Some default values
			switch($field) {
			case 'timezone':
				return config('date.timezone', date_default_timezone_get());
			case 'language':
				return config('site.language', 'en');
			}
		}

		return $this->{$field};
	}

	public function __set($field, $value) {

		if (empty(User::$editable_fields[$field]))
			return FALSE;

		switch ($field) {
		case 'pass':
			$value = user_hash_password($value);
			session_regenerate();
			break;
		case 'rid':
			if($this->uid && user_has_role(MODERATOR_RID) && ANONYMOUS_RID < $value && $value <= AUTHENTICATED_RID) {
				# OK
			} else {
				return FALSE;
			}
		}

		if($this->{$field} !== $value) {
			$this->{$field} = $value;
			$this->updated = TRUE;
		}

		return TRUE;
	}

	public function data($key, $default = NULL) {
		return val($this->data[$key], $default);
	}

	public function set_data($key, $value = NULL) {
		$this->updated = TRUE;

		if(is_array($key)) {
			foreach($key as $k => $v) {
				$this->set_data($k, $v);
			}
		} elseif(is_null($value)) {
			unset($this->data[$key]);
		} else {
			$this->data[$key] = $value;
		}
	}

	public function __destruct() {
		$this->save();
	}

	public function save() {
		if($this->uid && $this->updated) {
			$fields = array('data' => serialize($this->data));

			foreach(User::$editable_fields as $field => $editable) {
				if ($editable) {
					$fields[$field] = $this->{$field};
				}
			}

			$this->updated = FALSE;

			return (bool) db_update('users')
				->fields($fields)
				->condition('uid', $this->uid)
				->execute();
		}
	}
}

class AnonymousUser extends User {

	function __construct() {
		$this->uid = 0;
		$this->hostname = ip_address();
		$this->rid = ANONYMOUS_RID;
		$this->name = t('Guest');
	}

	public function data($key, $default = NULL) {
		return val($_SESSION['user_data'][$key], $default);
	}

	public function set_data($key, $value = NULL) {
		if(is_array($key)) {
			foreach($key as $k => $v) {
				$this->set_data($k, $v);
			}
		} elseif(is_null($value)) {
			unset($_SESSION['user_data'][$key]);
		} else {
			$_SESSION['user_data'][$key] = $value;
		}
	}

	public function save() {}
}

function anonymous_user() {
	return new AnonymousUser;
}

function user_has_role($role) {
	global $user;

	if($user->uid == 0 || $user->rid == ANONYMOUS_RID) {
		return $role == ANONYMOUS_RID;
	}
	else {
		return $user->rid <= $role;
	}
}

function user_role_required($role = AUTHENTICATED_RID) {
	global $request_path;

	if(user_has_role(AUTHENTICATED_RID) && !user_has_role($role)) {
		show_403();
	}
	else if(!user_has_role(AUTHENTICATED_RID) && $request_path !== 'account/login') {
		redirect('account/login', array('redirect' => $request_path));
	}
	else {
		# Good to go !
	}
}

function user_check_password($pwd) {
	$min = (int) config('user.pwd.min', 6);

	if(config('user.pwd.secure', TRUE)) {

		if( !preg_match("#[0-9]+#", $pwd) ) {
			message(MESSAGE_ERROR, t('Passwords must include at least one number.'));
			return FALSE;
		}

		if( !preg_match("#[a-z]+#", $pwd) ) {
			message(MESSAGE_ERROR, t('Passwords must include at least one letter.'));
			return FALSE;
		}

		if( !preg_match("#[A-Z]+#", $pwd) ) {
			message(MESSAGE_ERROR, t('Passwords must include at least one capital letter.'));
			return FALSE;
		}
	}

	if( strlen($pwd) < $min ) {
		message(MESSAGE_ERROR, t('Passwords must be at least @min chars long.', array('@min' => $min)));
		return FALSE;
	}

	return TRUE;
}

function user_hash_password($pass) {
	return sha1($pass);
}

function user_setup_environment() {
	global $user, $language, $language_custom;
	static $locales;

	if (!isset($locales)) $locales = config('site.locales', array('en' => 'en_US'));

	if(empty($language_custom) && !empty($_SESSION['language']))
		$language_custom = $_SESSION['language'];
	else if($language_custom != $language)
		$_SESSION['language'] = $language_custom;
	else
		unset($_SESSION['language']);

	lang_set($user->language);

	@date_default_timezone_set($user->timezone);

	if (isset($locales[$language])) {
		setlocale(LC_ALL, $locales[$language]);
		setlocale(LC_NUMERIC, 'en_US');
	} else {
		setlocale(LC_ALL, 'en_US');
	}
}

function user_login($mail, $pass, $action = 'login') {
	global $user;

	if(user_has_role(AUTHENTICATED_RID)) {
		message(MESSAGE_ERROR, t('You are already logged in.'));
		return FALSE;
	}

	switch ($action) {
	case 'reset':
	case 'activate':

		$ua = db_select('users_actions', 'ua')
			->fields('ua', array('uid'))
			->condition('ua.salt', $pass)
			->condition('ua.action', $action)
			->condition('ua.time', REQUEST_TIME - 86400, '>')
			->execute()
			->fetch();

		db_delete('users_actions')
			->condition('salt', $pass)
			->condition('action', $action)
			->execute();

		$user_data = !$ua ? NULL :
			db_query(
				'SELECT * FROM {users} u WHERE uid = :uid',
				array(':uid' => $ua->uid)
			)
			->fetchObject('User');
		break;

	case 'login':

		$user_data = db_query(
			'SELECT * FROM {users} u WHERE mail = :mail AND pass = :pass AND status',
			array(
				':mail' => $mail,
				':pass' => user_hash_password($pass)
			))
			->fetchObject('User');

		break;

	default:
		return FALSE;
	}

	if ($user_data) {
		$user = $user_data;
		$user->data = @unserialize($user->data);

		session_regenerate();

		$user->login = REQUEST_TIME;
		$user->status = 1;

		message(MESSAGE_SUCCESS, t('You were successfully logged in.'));
		return TRUE;
	}

	return FALSE;
}

function user_register($data) {
	global $user;

	$language = $user->language ? $user->language : config('site.language', 'en');
	$timezone = $user->timezone ? $user->timezone :  config('date.timezone', date_default_timezone_get());

	$data = array(
		'created' => REQUEST_TIME,
		'status' => 0,
		'rid' => AUTHENTICATED_RID,
	) + array_intersect_key($data, array(
		'name' => NULL,
		'mail' => NULL,
		'pass' => NULL,
		'language' => NULL,
		'timezone' => NULL,
	)) + array(
		'mail' => NULL,
		'pass' => NULL,
		'language' => $language,
		'timezone' => $timezone,
	);

	if (!user_check_password($data['pass'])) {
		message(MESSAGE_ERROR, t('This password is not valid.'));
		return FALSE;
	}

	if (!is_email($data['mail'])) {
		message(MESSAGE_ERROR, t('This email adress is not valid.'));
		return FALSE;
	}

	$data['pass'] = strlen($data['pass']) ? user_hash_password($data['pass']) : NULL;

	try {
		$uid = db_insert('users')->fields($data)->execute();
	} catch(PDOException $e) {
		message(MESSAGE_ERROR, t('This email adress is already registered.'));
		return FALSE;
	}

	user_action_request($uid, 'activate');

	return TRUE;
}

function user_action_request($uid, $action) {

	if($account = user_get($uid)) {

		require_once 'mail.php';
		require_once 'theme.php';

		try {
			db_insert('users_actions')
				->fields(array(
					'uid' => $account->uid,
					'action' => $action,
					'salt' => $salt = random_salt(32),
					'time' => REQUEST_TIME,
				))
				->execute();

			mail_send(array(
				'to' => $account->mail,
				'subject' => t('[@name] Account @action', array(
					'@name' => config('site.name'),
					'@action' => t($action))),
				'text' => theme('account/mail/'.$action, array(
					'reset_url' => url('account/reset/'.$salt),
					'activate_url' => url('account/activate/'.$salt),
					'account' => $account)),
			));

		} catch (PDOException $e) {
			if(DEVEL) dvm($e);
		}
	}

	message(MESSAGE_SUCCESS, t('Further instructions have been sent to your e-mail address.'));
}

function user_logout() {
	global $user;
	if($user->uid) {
		$user = anonymous_user();
		session_regenerate();
		message(MESSAGE_SUCCESS, t('You were successfully logged out.'));
		return TRUE;
	} else {
		return FALSE;
	}
}

function &user_get($uid = NULL) {
	global $user;
	static $accounts = array();

	if ($uid === NULL || $user->uid == $uid) {
		return $user;
	}

	if(is_numeric($uid))
		$account = db_query('SELECT * FROM {users} u WHERE uid = :uid', array(':uid' => $uid))->fetchObject('User');
	else
		$account = db_query('SELECT * FROM {users} u WHERE mail = :mail', array(':mail' => $uid))->fetchObject('User');

	if ($account) {
		$account->data = @unserialize($account->data);
	}

	return $account;
}