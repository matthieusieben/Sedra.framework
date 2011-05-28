<?php

require_once 'exception.php';

Load::user();

XSRF::init();

/**
 * Simple XSRF Protection
 *
 * This class provides a simple method of protecting form submission from
 * common Cross Site Request Forgery (XSRF) attacks.
 *
 * Protection is accomplished by adding a randomised hidden field to forms that
 * are checked when the form is processed. If the hidden field doesn't exist,
 * or is modified then the request should be rejected.
 *
 * The method used is stateless and does not require any session management to
 * be used. This allows the request to be easily handled by a load balanced
 * cluster of frontends that don't share session information.
 *
 * Protection against replay attacks is also provided using this same method.
 *
 * Based on 'Simple XSRF Protection' by David Parrish <david@dparrish.com>.
 *
 * @version 1.0
 * @copyright Copyright (c) 2010, David Parrish
 * @author David Parrish <david@dparrish.com>
 * @package XsrfProtection
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

class XSRF {

	/**
	 * Validation successful, this is likely not a forged request.
	 */
	const kCheckSuccess = 0;
	/**
	 * Validation failed, the security token was invalid.
	 */
	const kCheckInvalid = 1;
	/**
	 * Validation failed, the security token has expired.
	 */
	const kCheckExpired = 2;
	/**
	 * Validation failed, the security token was missing.
	 */
	const kCheckMissing = 3;
	/**
	 * Validation failed, the security token had already been used.
	 */
	const kCheckReused = 4;

	/**
	 * Sets the hash secret key.
	 *
	 * This is required for the protection to work. If you don't set this key,
	 * you will get a PHP error in the log.
	 *
	 * DO NOT use the example key, as it is the only protection provided by this
	 * class. If you use the example key, forging requests becomes trivial.
	 *
	 * @param string $key The new hash secret key
	 */
	private static $key;
	
	/**
	 * Enable stateful mode.
	 *
	 * Stateful mode adds an extra level of security, only allowing tokens to be
	 * used a single time. This prevents replay attacks, but requires use of the
	 * PHP session cache.
	 *
	 * @param bool $stateful Whether stateful mode should be enabled.
	 */
	private static $stateful;
	
	/**
	 * Set the maximum age of tokens.
	 * If a request is received with a token that is older than this, it is
	 * rejected.
	 *
	 * @param int $age The maximum age of tokens in seconds. This defaults to 1
	 * hour if not specified.
	 */
	private static $timeout;

	public static function init()
	{
		self::$key = config('xsrf/key');
		self::$stateful = config('xsrf/stateful', TRUE);
		self::$timeout = config('xsrf/timeout', 3600);
	}

	public static function setStateful($value = TRUE)
	{
		self::$stateful = $value;
	}

	/**
	 * Get the value that would be set in the hidden field.
	 *
	 * You could for example use this in a GET request.
	 *
	 * @return string The token value
	 */
	public static function ProtectionFieldValue()
	{
		if(!self::$key) {
			trigger_error("XsrfProtection failure: No secret key has been set", E_USER_ERROR);
			return NULL;
		}
		return base64_encode(hash_hmac("sha256", self::ProtectionData(time()),
			self::$key). ":". time());
	}

	/**
	 *
	 * @access private
	 */
	private static function ProtectionData($time)
	{
		return $time . ':' . User::uid();
	}

	/**
	 * Return the hidden input field that contains the secret token.
	 *
	 * This is the only method you are required to call when generating the form.
	 *
	 * @return string The complete <input> tag suitable for printing.
	 */
	public static function Field($name = '__xsrfprotect_tok')
	{
		return '<input type="hidden" name="'.$name.'"  value="'.self::ProtectionFieldValue().'" />';
	}

	public static function Validate($req = NULL, $field_name = '__xsrfprotect_tok')
	{
		if(!self::$key) {
			trigger_error("XsrfProtection failure: No secret key has been set", E_USER_ERROR);
			return self::kCheckInvalid;
		}
		
		if ($req === NULL)
			$req = $_REQUEST;

		if (!is_array($req)) {
			trigger_error("XsrfProtection failure: Missing request array", E_USER_WARNING);
			return self::kCheckMissing;
		}

		if (!isset($req[$field_name])) {
			trigger_error("XsrfProtection failure: Missing token", E_USER_WARNING);
			return self::kCheckMissing;
		}

		$decdata = base64_decode($req[$field_name]);
		$parts = explode(":", $decdata, 2);
		if (!$parts || count($parts) != 2) {
			trigger_error("XsrfProtection failure: Broken token data", E_USER_WARNING);
			return self::kCheckInvalid;
		}

		$teststr = hash_hmac("sha256", self::ProtectionData($parts[1]), self::$key);

		if ($teststr != $parts[0]) {
			trigger_error("XsrfProtection failure: Invalid token", E_USER_WARNING);
			return self::kCheckInvalid;
		}

		if ($parts[1] < time() - self::$timeout) {
			trigger_error("XsrfProtection failure: Token has expired", E_USER_WARNING);
			return self::kCheckExpired;
		}

		if (self::$stateful) {
			if (!isset($_SESSION['__xsrfprotection_used_keys']))
				$_SESSION['__xsrfprotection_used_keys'] = array();
			if (isset($_SESSION['__xsrfprotection_used_keys'][$teststr])) {
				trigger_error("XsrfProtection failure: Token has already been used", E_USER_WARNING);
				return self::kCheckReused;
			}
			$_SESSION['__xsrfprotection_used_keys'][$teststr] = true;
		}

		return self::kCheckSuccess;
	}

	public static function check($req = NULL, $field_name = '__xsrfprotect_tok')
	{
		if(self::validate($req, $field_name) !== self::kCheckSuccess) {
			throw new XSRFException();
			return FALSE;
		}
		return TRUE;
	}
}


