<?php

$language_default = config('site.language', 'en');
$language_custom = @$_GET['language'];
$languages = config('site.languages', array('en' => 'English'));
$language = $language_default;
$strings = array();

lang_set($language_custom);

function lang_load($l) {
	global $strings, $languages;

	if(empty($strings[$l]) && isset($languages[$l])) {
		$strings[$l] = array();

		if (is_file(FRAMEWORK_ROOT.'/languages/'.$l.'.php')) {
			require_once FRAMEWORK_ROOT.'/languages/'.$l.'.php';
		}

		if (is_file(APP_ROOT.'/languages/'.$l.'.php')) {
			require_once APP_ROOT.'/languages/'.$l.'.php';
		}

		# TODO : load from database
	}
}

function lang_set($l) {
	global $language, $language_custom, $languages;

	if(empty($language_custom) || $l == $language_custom)
		if(array_key_exists($l, $languages))
			return $language = $l;

	return $language = $language_custom;
}

function lang_list($lang = NULL) {
	global $language, $languages;
	static $lang_list;
	if (is_null($lang)) $lang = $language;
	if (!isset($lang_list[$lang])) {
		foreach($languages as $code => $name) {
			$lang_list[$lang][$code] = t($name);
		}
	}
	return $lang_list[$lang];
}

function lang_name($lang = NULL) {
	global $language;
	if (is_null($lang)) $lang = $language;
	$list = lang_list($lang);
	return isset($list[$lang]) ? $list[$lang] : NULL;
}

function t($string, $replace_pairs = array()) {
	global $strings, $language;

	lang_load($language);

	if (isset($strings[$language][$string])) {
		# Translation string exists
		$string = $strings[$language][$string];
	}
	# else : Not found, not translated
	# TODO : add in database when not found.

	# Transform arguments before inserting them.
	foreach ($replace_pairs as $key => $value) {
		switch ($key[0]) {
		case '@':
		default:
			$replace_pairs[$key] = check_plain($replace_pairs[$key]);
			break;

		case '!': # Do not escape the string
		}
	}
	return strtr($string, $replace_pairs);
}
