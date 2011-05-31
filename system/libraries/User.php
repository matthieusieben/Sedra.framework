<?php

# User methods work together with sessions
require_once 'Session.php';

class User {
	protected $uid = ANONYMOUS_UID;
	protected $rid = ANONYMOUS_RID;
	protected $language = REFERENCE_LANGUAGE;
	protected $data = array();

	protected $name;
	protected $pass;
	protected $mail;
	protected $timezone;
	protected $created;
	protected $access;
	protected $login;
	protected $status;

	public function __construct($user_data)
	{
		$this->uid = $user_data['uid'];
		$this->data = unserialize($user_data['data']);

		foreach(User::$fields as $field) {
			$this->{$field} = $user_data[$field];
		}
	}

	public function data($key, $default = NULL) {
		return val($this->data[$key], $default);
	}

	public function set_data($key, $value = NULL) {
		$this->was_updated();

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

	public function __get($key)
	{
		return isset($this->{$key}) ? $this->{$key} : NULL;
	}

	public function __set($field, $value)
	{
		# Do some validation
		switch ($field) {
		case 'pass':
			$value = sha1($value);
			break;
		case 'language':
			$value = Lang::validate($value);
			break;
		default:
			if(!in_array($field, User::$fields)) {
				return FALSE;
			}
			break;
		}

		$this->was_updated();
		$this->{$field} = $value;
		
		return TRUE;
	}

	/**
	 * undocumented function
	 *
	 * @return	void
	 * @todo	Check that User::current() has the right to do this.
	 */
	public function save()
	{
		$fields = array(
			'data' => serialize($this->data),
			);

		foreach(self::$fields as $field) {
			$fields[$field] = $this->{$field};
		}
		
		db_update('users')
	    	->fields($fields)
	    	->condition('uid', $this->uid)
	    	->execute();
	}

	private $updated = FALSE;
	private function was_updated()
	{
		if(!$this->updated) {
			$this->updated = TRUE;
			Hook::register(HOOK_SHUTDOWN, array($this,'save'));
		}
	}

	/************************************************************************/
	/*                    Static functions and variables                    */
	/************************************************************************/

	public static $user = NULL;
	public static $fields = array('rid','name','pass','mail','language','timezone','created','access','login','status');

	public static function set(User $user)
	{
		self::$user =& $user;
		Lang::set($user->language);
	}

	public static function &current()
	{
		return self::$user;
	}

	public static function connected()
	{
		return self::$user->uid !== ANONYMOUS_UID;
	}

	public static function authenticate($name, $pass)
	{
		$pass = sha1($pass);

		$user_data = db_query('SELECT * FROM {users} u WHERE (name = :name OR mail = :name) AND pass = :pass', array(':name' => $name, ':pass' => $pass))->fetchAssoc();

		# Regenerate the session ID to prevent against session fixation attacks.
		Session::regenerate();

		if($user_data) {
			$user = new User($user_data);

			# Update last login field
			# Setter will not be called from this class, we have to call it manually.
			$user->__set('login', REQUEST_TIME);

			User::set($user);

			message(MESSAGE_SUCCESS, 'You were successfully logged in.');

			return TRUE;
		}
		else {
			User::set(new AnonymousUser);
			message(MESSAGE_WARNING, 'Wrong username or password.');
			return FALSE;
		}
	}

	public static function logout()
	{
		# Regenerate the session ID to prevent against session fixation attacks.
		Session::regenerate();
		User::set(new AnonymousUser);
		message(MESSAGE, 'You are now logged out.');
		return TRUE;
	}
}

class AnonymousUser extends User {

	public function __construct() {
		$this->timezone = config('timezone');

		$user_data = Input::session('user_data');

		if(isset($user_data['data'], $user_data['language'])) {
			$this->data = unserialize($user_data['data']);
			$this->language = $user_data['language'];
		} else {
			$this->language = Lang::detect();
		}
	}

	public function save() {
		$user_data = array(
			'data' => serialize($this->data),
			'language' => $this->language,
		);
		Output::session('user_data', $user_data);
	}
}
