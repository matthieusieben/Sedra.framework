<?php

require_once 'functions/config.php';
require_once 'functions/timer.php';
require_once 'functions/http.php';
require_once 'functions/server.php';

function ob_get_clean_all()
{
	$output = '';

	# Previous output
	while(ob_get_level() > 0) {
		$output = ob_get_clean() . $output;
	}

	return $output;
}

/**
 * Encode special characters in a plain-text string for display as HTML.
 *
 * @param	$text	The text to be checked or processed.
 * @return
 * 		An HTML safe version of $text, or an empty string if $text is not
 * 		valid UTF-8.
 */
function html($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'utf-8');
}

/**
 * Return an HTML safe dump of the given variable(s) surrounded by "pre" tags.
 * You can pass any number of variables (of any type) to this function.
 *
 * @param mixed
 * @return string
 */
function dump()
{
	if(Hook::registered(HOOK_DUMP)) {
		foreach(func_get_args() as $v) {
			Hook::call(HOOK_DUMP, $v);
		}
	}
	elseif(@ini_get('html_errors')) {
		foreach(func_get_args() as $v) {
			var_dump($v);
		}
	}
	else {
		foreach(func_get_args() as $v) {
			echo '<pre>'. html($v===NULL ? 'NULL' : (is_scalar($v)?$v:print_r($v,1))) ."</pre>\n";
		}
	}
}

/**
 * Set a message to show to the user (MESSAGE_SUCCESS, MESSAGE_WARNING, or MESSAGE_ERROR).
 *
 * @param string $type of message
 * @param string $value the message to store. This value will be translated.
 * @return mixed the array of messages if $value is not specified
 */
function message($type = NULL, $value = NULL, $strings = array())
{
	static $messages = array();

	if($value) {
		return $messages[$type][] = t($value, $strings);
	}
	elseif($type) {
		return val($messages[$type]);
	}
	else {
		return $messages;
	}
}

/**
 * System registry object for storing global values
 *
 * @param string $k the object name
 * @param mixed $v the object value
 * @return mixed the value stored in $k
 */
function reg($k, $v=null)
{
	static $r;
	return (func_num_args() > 1 ? $r[$k] = $v: (isset($r[$k]) ? $r[$k] : NULL));
}

/**
 * Shortcut for Lang::t()
 *
 * @param string $string 
 * @param array $replace_pairs 
 * @param string $language 
 * @return string
 * @see Lang::t()
 */
function t($string, $replace_pairs = array(), $language = NULL)
{
	return Lang::t($string, $replace_pairs, $language = NULL);
}

/**
 * Prints the result of t().
 *
 * @param	string	$string
 * @param	array	$replace_pairs
 * @param	string	$language
 * @return	string
 * @see	Lang::t()
 */
function p($string, $replace_pairs = array(), $language = NULL)
{
	echo Lang::t($string, $replace_pairs, $language = NULL);
}

/**
 * Checks if an integer has some flags set.
 *
 * @param int $value 
 * @param int $flags 
 * @return boolean True iif $value has all the '1' bits of $flags set to '1'
 */
function check_flags($value, $flags)
{
	return ($value & $flags) === $flags;
}

/**
 * Get a value if it is set of a default value otherwise.
 *
 * @param mixed $value 
 * @param mixed $default 
 * @return isset(value) ? value : default
 */
function val(&$value, $default = NULL)
{
	return isset($value) ? $value : $default;
}
