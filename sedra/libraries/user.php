<?php

require_once 'language.php';

/**
 * User
 */
class User
{
	protected static $user;

	public static function set($user)
	{
		# Get the language from $user or try to detect it
		$language = empty($user->language) ? Language::detect() : $user->language;

		# Load the language
		$user->language = Language::set($language);

		self::$user = $user;
	}

	public static function get()
	{
		return self::$user;
	}

	public static function authenticate($info)
	{
		$data = array( 'pass' => $info['pass'], 'status'=> 1 );
		if(isset($into['mail'])) $data['mail'] = $info['mail'];
		else $data['name'] = $info['name'];

		Load::model('users');
		$account = Users::load($data);

		if($account)
		{
			$account->login = REQUEST_TIME;
			db_update('users')
		    	->fields(array('login'=>REQUEST_TIME))
		    	->condition('uid', $account->uid)
		    	->execute();

			// Regenerate the session ID to prevent against session fixation attacks.
			Session::regenerate();

			self::set($account);

			return TRUE;
		}
		else
		{
			self::set(self::make_anonymous());

			return FALSE;
		}
	}

	public static function logout()
	{
		// Regenerate the session ID to prevent against session fixation attacks.
		Session::regenerate();

		self::set(self::make_anonymous());
	}

	public static function uid()
	{
		return isset(self::$user->uid) ? self::$user->uid : ANONYMOUS_UID;
	}

	public static function rid()
	{
		return isset(self::$user->rid) ? self::$user->rid : ANONYMOUS_RID;
	}

	public static function language()
	{
		return self::$user->language;
	}

	public static function data($field, $default = NULL)
	{
		if(self::connected()) {
			return akon(self::$user->data, $field, $default);
		} else {
			return isset($_SESSION['data']) ? akon($_SESSION['data'], $field, $default) : $default;
		}
	}

	public static function set_data($new_data)
	{
		return self::save(array('data' => $new_data));
	}

	public static function save($fields)
	{
		if(self::connected()) {
			Load::model('users');

			$new_fields = array();

			foreach ($fields as $key => $value) {
				# If the field is valid
				if(isset(self::$user->$key)) {
					switch ($key) {
					case 'session':
						continue;

					case 'data':
						if(!empty(self::$user->data))
							$new_value = $value + self::$user->data;
						else
							$new_value = $value;
						$new_fields[$key] = serialize($new_value);
						break;

					case 'pass':
						$new_fields[$key] = $value;
						$new_value = md5($value);
						break;

					default:
						$new_fields[$key] = $value;
						$new_value = $value;
						break;
					}

					self::$user->$key = $new_value;
				}
			}

			return Users::update(self::uid(), $new_fields);
		}
		else
		{
			# Save in session data if not logged in
			$_SESSION = $fields + $_SESSION;

			return TRUE;
		}
	}

	public static function allowed($action)
	{
		Load::model('permissions');
		return Permissions::role(self::rid(), $action);
	}

	public static function connected()
	{
		return isset(self::$user->uid) && (self::$user->uid !== ANONYMOUS_UID);
	}

	/************************************************************************/
	/*                                Tools                                 */
	/************************************************************************/

	public static function make_anonymous($session='')
	{
		$user = new stdClass();
		$user->uid = ANONYMOUS_UID;
		$user->rid = ANONYMOUS_RID;
		$user->hostname = ip_address();
		$user->session = $session;
		return $user;
	}
}
