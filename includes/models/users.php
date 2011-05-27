<?php

/**
 * Users
 */
class Users
{
	public static function load($array = array())
	{
		Load::db();

		// Dynamically compose a SQL query:
		$query = array();
		$params = array();

		if (is_numeric($array)) {
			$array = array('uid' => $array);
		}
		elseif (!is_array($array)) {
			return FALSE;
		}

		foreach ($array as $key => $value) {
			if ($key == 'uid' || $key == 'status') {
				$query[] = "u.$key = :$key";
				$params[":$key"] = $value;
			}
			else if ($key == 'pass') {
				$query[] = "u.pass = :pass";
				$params[":pass"] = md5($value);
			}
			else {
				$query[]= "LOWER(u.$key) = LOWER(:$key)";
				$params[":$key"] = $value;
			}
		}
		$user = db_query('SELECT * FROM {user} u WHERE '. implode(' AND ', $query), $params);

		if (!$user) {
			return FALSE;
		}

		$user->data = unserialize($user->data);

		return $user;
	}

	public static function update($uid, $fields)
	{
		$new_fields = array();

		foreach ($fields as $key => $value) {
			switch ($key) {
			case 'data':
				if(empty($value))
					$new_fields[$key] = serialize(array());
				elseif(is_array($value))
					$new_fields[$key] = serialize($value);
				elseif(@unserialize($value))
					$new_fields[$key] = $value;
				break;

			case 'pass':
				$new_fields[$key] = md5($value);
				break;

			case 'status':
				$new_fields[$key] = $value ? '1' : '0';
				break;

			case 'language':
				Load::library('language');
				if(Language::validate($value) == $value)
					$new_fields[$key] = $value;
				break;

			case 'name':
			case 'mail':
			case 'timezone':
				$new_fields[$key] = $value;
				break;

			default:
				continue;
			}
		}
		
		if(!empty($new_fields)) {
			# TODO : update values in DB
		}
		else return FALSE;
	}
}
