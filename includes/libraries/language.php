<?php

// Language in which are written reference strings (English).
define('REFERENCE_LANGUAGE', 'en');

class Language
{
	/**
	 * The language currently displayed
	 *
	 * @var	string
	 */
	private static $language;

	/**
	 * Table containing the sentences in the languages already translated
	 *
	 * @var	array
	 */
	private static $translated_strings = array();

	/**
	 * List files already loaded in memory
	 *
	 * @var	array
	 */
	private static $loaded_files = array();

	/**
	 * List of all available languages
	 *
	 * @var	array
	 */
	public static $available_languages = array(
		'af' => 'Afrikaans',
		'sq' => 'Albanian',
		'ar' => 'Arabic',
		'hy' => 'Armenian',
		'as' => 'Assamese',
		'az' => 'Azeri',
		'eu' => 'Basque',
		'be' => 'Belarusian',
		'bn' => 'Bengali',
		'bg' => 'Bulgarian',
		'ca' => 'Catalan',
		'zh' => 'Chinese',
		'hr' => 'Croatian',
		'cs' => 'Czech',
		'da' => 'Danish',
		'div' => 'Divehi',
		'nl' => 'Dutch',
		'en' => 'English',
		'et' => 'Estonian',
		'fo' => 'Faeroese',
		'fa' => 'Farsi',
		'fi' => 'Finnish',
		'fr' => 'French',
		'mk' => 'FYRO Macedonian',
		'gd' => 'Gaelic',
		'ka' => 'Georgian',
		'de' => 'German',
		'el' => 'Greek',
		'gu' => 'Gujarati',
		'he' => 'Hebrew',
		'hi' => 'Hindi',
		'hu' => 'Hungarian',
		'is' => 'Icelandic',
		'id' => 'Indonesian',
		'it' => 'Italian',
		'ja' => 'Japanese',
		'kn' => 'Kannada',
		'kk' => 'Kazakh',
		'kok' => 'Konkani',
		'ko' => 'Korean',
		'kz' => 'Kyrgyz',
		'lv' => 'Latvian',
		'lt' => 'Lithuanian',
		'ms' => 'Malay',
		'ml' => 'Malayalam',
		'mt' => 'Maltese',
		'mr' => 'Marathi',
		'mn' => 'Mongolian',
		'ne' => 'Nepali',
		'no' => 'Norwegian',
		'or' => 'Oriya',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'pa' => 'Punjabi',
		'rm' => 'Rhaeto-Romanic',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'sa' => 'Sanskrit',
		'sr' => 'Serbian',
		'sk' => 'Slovak',
		'ls' => 'Slovenian',
		'sb' => 'Sorbian',
		'es' => 'Spanish',
		'sx' => 'Sutu',
		'sw' => 'Swahili',
		'sv' => 'Swedish',
		'syr' => 'Syriac',
		'ta' => 'Tamil',
		'tt' => 'Tatar',
		'te' => 'Telugu',
		'th' => 'Thai',
		'ts' => 'Tsonga',
		'tn' => 'Tswana',
		'tr' => 'Turkish',
		'uk' => 'Ukrainian',
		'ur' => 'Urdu',
		'uz' => 'Uzbek',
		'vi' => 'Vietnamese',
		'xh' => 'Xhosa',
		'yi' => 'Yiddish',
		'zu' => 'Zulu'
	);

	public static function detect()
	{
		if( isset($_GET['language']) && $_GET['language'] )
		{
			return $_GET['language'];
		}
		
		return config('language/default');
	}

	/**
	 * Set the language to use.
	 *
	 * @param	string $language	The language in which the future strings must be translated
	 * @return	string	The language validated and actually set
	 */
	public static function set($language)
	{
		self::$language = self::validate($language);
		self::load_file(INCLUDE_DIR.'language/');
		return self::$language;
	}

	/**
	 * Checks if a language is supported by the system
	 *
	 * @param	mixed $language	The language to check (or a list)
	 * @return	string A valid version of $language
	 */
	public static function validate($language)
	{
		foreach((array) $language as $l)
		{
			if(isset(self::$available_languages[$l]))
			{
				return $l;
			}
		}

		return config('language/default');
	}

	/**
	 * Get the language currently being used.
	 *
	 * @return	string	The language currently being used
	 */
	public static function get()
	{
		return self::$language;
	}

	/**
	 * Prints a translation of a string.
	 *
	 * @see Language::t()
	 */
	public static function p($string, $replace_pairs = array())
	{
		echo self::t($string, $replace_pairs);
	}

	/**
	 * Translate a string from REFERENCE_LANGUAGE in the language being currenly
	 * used.
	 *
	 * @param	string $string		The string to translate
	 * @param	array $replace_pairs	An array in the form array('from' => 'to', ...) of strings to replace. If 'from' doesn't begin with '!', 'to' will be escaped.
	 * @return	string	The translated string
	 */
	public static function t($string, $replace_pairs = array())
	{
		if ( isset( self::$translated_strings[self::$language][$string] ) )
		{
			// Translation string exists
			$string = self::$translated_strings[self::$language][$string];
		}
		// else : Not found, not translated

		if (empty($replace_pairs))
		{
			return $string;
		}
		else
		{
			// Transform arguments before inserting them.
			foreach ($replace_pairs as $key => $value)
			{
				switch ($key[0])
				{
					case '@':
					default:
					$replace_pairs[$key] = check_plain($value);
					break;

					case '!': // Do not escape the string
				}
			}
			return strtr($string, $replace_pairs);
		}
	}

	/**
	 * Load a language file from any directory.
	 *
	 * @access	private
	 * @param	string	$path	A folder containing language files
	 * @return	bool	True if the file was loaded
         * @todo	log an error if no file is loaded
	 */
	private static function load_file( $path )
	{
		// No need to be loaded
		if ( self::$language === REFERENCE_LANGUAGE )
			return TRUE;

		$file = $path . self::$language.'.php';

		// Already loaded ?
		if ( isset(self::$loaded_files[$file]) )
			return self::$loaded_files[$file];

		// Initialize array
		if ( !isset(self::$translated_strings[self::$language]) )
			self::$translated_strings[self::$language] = array();

		if( @include($file) )
		{
			// Check validity
			if ( (!isset($lang)) || (!is_array($lang)) )
			{
				self::$loaded_files[$file] = FALSE;
			}
			else
			{
				self::$translated_strings[self::$language] += $lang; // array_merge
				self::$loaded_files[$file] = TRUE;
			}
		}
		else
		{
			self::$loaded_files[$file] = FALSE;
		}

		return self::$loaded_files[$file];
	}
}