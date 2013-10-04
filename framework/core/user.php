<?php

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
		'data' => TRUE,
		'language' => TRUE,
		'timezone' => TRUE,
		'created' => TRUE,
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
			return NULL;

		switch ($field) {
		case 'pass':
			$value = sedra_password_hash($value);
			session_regenerate();
			break;
		case 'rid':
			if($this->uid && user_has_role(MODERATOR_RID) && AUTHENTICATED_RID <= $value && $value <= ADMINISTRATOR_RID) {
				# OK
			} else {
				return NULL;
			}
		}

		if($this->{$field} !== $value)
			$this->updated = TRUE;

		return $this->{$field} = $value;
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
		return TRUE;
	}
}

class AnonymousUser extends User {

	function __construct() {
		$this->uid = 0;
		$this->hostname = ip_address();
		$this->rid = ANONYMOUS_RID;
		$this->name = t('Guest');
		$this->language = config('site.language', 'en');
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
}

function user_has_role($role) {
	global $user;

	if($role < 0) {
		return TRUE;
	}
	else if($role == ANONYMOUS_RID) {
		return $user->uid == 0;
	}
	else {
		return $user->rid >= $role;
	}
}

function user_role_required($role) {
	if(!user_has_role($role)) {
		show_401();
	}
}

function user_check_password($pwd, &$error_message = NULL) {
	$min = (int) config('user.pwd.min', 6);

	if(config('user.pwd.secure', TRUE)) {

		if( !preg_match("#[0-9]+#", $pwd) ) {
			$error_message = t('Passwords must include at least one number.');
			return FALSE;
		}

		if( !preg_match("#[a-z]+#", $pwd) ) {
			$error_message = t('Passwords must include at least one letter.');
			return FALSE;
		}

		if( !preg_match("#[A-Z]+#", $pwd) ) {
			$error_message = t('Passwords must include at least one capital letter.');
			return FALSE;
		}
	}

	if( strlen($pwd) < $min ) {
		$error_message = t('Passwords must be at least @min chars long.', array('@min' => $min));
		return FALSE;
	}

	return TRUE;
}

function user_setup_environment() {
	global $user, $language, $language_custom, $language_default;

	if(empty($language_custom) && isset($_SESSION['language']))
		$language_custom = $_SESSION['language'];

	if($language_custom == $language_default && !$user->uid)
		unset($_SESSION['language']);
	else if($language_custom == $user->language)
		unset($_SESSION['language']);
	else if($language_custom)
		$_SESSION['language'] = $language_custom;

	language_set($user->language);

	@date_default_timezone_set($user->timezone);
}

function user_login($mail, $pass, $action = 'login', &$error_message = NULL) {

	if(user_has_role(AUTHENTICATED_RID)) {
		$error_message = t('You are already logged in.');
		return FALSE;
	}

	$user_data = NULL;

	switch ($action) {
	case 'reset':
	case 'activate':

		db_delete('users_actions')
			->condition('time', REQUEST_TIME - 86400, '<')
			->execute();

		$ua = db_select('users_actions', 'ua')
			->fields('ua', array('uid'))
			->condition('ua.salt', $pass)
			->condition('ua.action', $action)
			->condition('ua.time', REQUEST_TIME - 86400, '>')
			->execute()
			->fetch();

		if(!$ua)
			return FALSE;

		db_delete('users_actions')
			->condition('salt', $pass)
			->condition('action', $action)
			->execute();

		$user_data = db_query(
				'SELECT * FROM {users} u WHERE uid = :uid',
				array(':uid' => $ua->uid))
			->fetchObject('User');
		break;

	case 'login':

		if(empty($pass))
			return FALSE;

		$user_data = db_query(
			'SELECT * FROM {users} u WHERE mail = :mail AND pass = :pass',
			array(
				':mail' => $mail,
				':pass' => sedra_password_hash($pass),
			))
			->fetchObject('User');

		if($user_data && !$user_data->status) {
			$error_message = t('You need to activate your account. Please check your email.');
			return FALSE;
		}
		break;

	default:
		return FALSE;
	}

	if ($user_data) {
		global $user;
		$user = $user_data;
		$user->data = @unserialize($user->data);

		session_regenerate();

		$user->login = REQUEST_TIME;
		$user->status = 1;

		switch ($action) {
		case 'reset':
			require_once 'includes/message.php';
			message(MESSAGE_SUCCESS, t('Your password has been reset. You should set a new one now.'));
			$user->pass = '';
			break;

		case 'activate':
			require_once 'includes/message.php';
			message(MESSAGE_SUCCESS, t('Your account has been activated.'));
			break;
		}

		return TRUE;
	} else {
		$error_message = t('Wrong email or password.');
	}

	return FALSE;
}

function user_register($data, &$error_message = NULL) {
	global $user;

	$data = array(
		'created' => REQUEST_TIME,
	) + array_intersect_key($data, array(
		'uid' => NULL,
		'rid' => NULL,
		'name' => NULL,
		'mail' => NULL,
		'pass' => NULL,
		'language' => NULL,
		'timezone' => NULL,
		'status' => NULL,
	)) + array(
		'rid' => AUTHENTICATED_RID,
		'mail' => NULL,
		'pass' => NULL,
		'language' => @$user->language ? $user->language : config('site.language', 'en'),
		'timezone' => @$user->timezone ? $user->timezone :  config('date.timezone', @date_default_timezone_get()),
		'status' => 0,
	);

	if (!user_check_password($data['pass'], $error_message)) {
		return FALSE;
	}

	if (!is_email($data['mail'])) {
		$error_message = t('This email adress is not valid.');
		return FALSE;
	}

	$data['pass'] = strlen($data['pass']) ? sedra_password_hash($data['pass']) : NULL;

	try {
		db_insert('users')->fields($data)->execute();
	} catch(PDOException $e) {
		$error_message = t('This email adress is already registered.');
		return FALSE;
	}

	user_action_request($data['mail'], 'activate');

	return TRUE;
}

function user_action_request($mail, $action) {

	if($account = user_find(array('mail' => $mail))) {
		load_module('mail');
		require_once 'includes/theme.php';

		if($action === 'reset')
			log_message('Password reset for "'.$mail.'"');

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
			'subject' => decode_plain(
				t('[@name] Account @action', array(
					'@name' => config('site.name'),
					'@action' => t($action),
				))
			),
			'text' => theme('account/mail/'.$action, array(
				'reset_url' => url('account/reset/'.$salt),
				'activate_url' => url('account/activate/'.$salt),
				'account' => $account,
			)),
		));
	}

	require_once 'includes/message.php';
	message(MESSAGE_SUCCESS, t('Further instructions have been sent to the e-mail address provided.'));
}

function user_logout() {
	global $user;
	if($user->uid) {
		$user = new AnonymousUser;
		session_regenerate();
		return TRUE;
	} else {
		return FALSE;
	}
}

function &user_find($cond) {
	$null = NULL;
	if(empty($cond))
		return $null;

	# Check wether we want the current user account
	{
		global $user;
		$is_cur = TRUE;
		foreach ($cond as $key => $value)
			if($user->$key != $value)
				{ $is_cur = FALSE; break; }

		if($is_cur)
			return $user;
	}

	# Find user in database
	{
		$query = db_select('users', 'u')->fields('u');
		foreach ($cond as $key => $value)
			$query->condition($key, $value);
		$account = $query->execute()->fetchObject('User');

		if ($account) {
			$account->data = (array) @unserialize($account->data);
			return $account;
		}
	}

	return $null;
}
