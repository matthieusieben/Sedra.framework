<?php

global $language_default;
global $language_custom;
global $languages;
global $language;
global $strings;

// Default values
$language_default = 'en';
$language_custom = @$_GET['language'];
$languages = array('en');
$language = $language_default;
$strings = array();

// Initialize after configuration file was loaded
hook_register('bootstrap', function() {
	global $language_default;
	global $language_custom;
	global $languages;
	global $language;

	$language_default = config('site.language', $language_default);
	$language_custom = @$_GET['language'];
	$languages = config('site.languages', $languages);
	$language = $language_default;
});

function language_load($l) {
	global $strings;

	foreach((array) explode(PATH_SEPARATOR, get_include_path()) as $module) {
		if(is_file($file = $module.'/languages/'.$l.'.php')) {
			require_once $file;
		}
	}
}

function language_is_valid($l) {
	global $languages;
	return empty($languages) || isset($languages[$l]);
}

function language_set($l) {
	global $language, $language_custom, $language_default;

	if($language_custom && language_is_valid($language_custom))
		$language = $language_custom;
	else if($l && language_is_valid($l))
		$language = $l;
	else
		$language = $language_default;

	$locales = config('site.locales', array('en' => 'en_US'));

	if (isset($locales[$language])) {
		setlocale(LC_ALL, $locales[$language]);
	} else {
		setlocale(LC_ALL, 'en_US');
	}
}

function language_list($lang = NULL) {
	global $language, $languages;
	static $language_list;
	if (is_null($lang)) $lang = $language;
	if (!isset($language_list[$lang])) {
		foreach($languages as $code => $name) {
			$language_list[$lang][$code] = t($name);
		}
	}
	return $language_list[$lang];
}

function language_name($lang = NULL) {
	global $language;
	if (is_null($lang)) $lang = $language;
	$list = language_list($lang);
	return isset($list[$lang]) ? $list[$lang] : NULL;
}

function t($string, $replace_pairs = array()) {
	global $strings, $language;

	if (isset($strings[$language][$string])) {
		# Translation string exists
		$string = $strings[$language][$string];
	}
	# else : Not found, not translated

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
