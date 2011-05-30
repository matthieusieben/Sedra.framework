<?php

class Lang
{
	/**
	 * The language currently used
	 *
	 * @var	string
	 */
	private static $language;

	/**
	 * Table containing the sentences in the languages already translated
	 *
	 * @var	array
	 */
	private static $strings = array();

	/**
	 * List of all valid languages
	 *
	 * @var	array
	 */
	public static $languages = array(
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
		
		return config('language', REFERENCE_LANGUAGE);
	}

	/**
	 * Set the language to use.
	 *
	 * @param	string $language	The language in which the future strings must be translated
	 * @return	string	The language validated and actually set
	 */
	public static function set($language)
	{
		$language = self::validate($language);
		return self::load($language) ? self::$language = $language : self::current();
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
			if(isset(self::$languages[$l]))
			{
				return $l;
			}
		}

		return config('language', REFERENCE_LANGUAGE);
	}

	/**
	 * Get the language currently being used.
	 *
	 * @return	string	The language currently being used
	 */
	public static function current()
	{
		return self::$language ? self::$language : REFERENCE_LANGUAGE;
	}

	/**
	 * Translate a string from REFERENCE_LANGUAGE in the language being currenly
	 * used.
	 *
	 * @param	string $string		The string to translate
	 * @param	array $replace_pairs	An array in the form array('from' => 'to', ...) of strings to replace. If 'from' doesn't begin with '!', 'to' will be escaped.
	 * @return	string	The translated string
	 */
	public static function t($string, $replace_pairs = array(), $language = NULL)
	{
		if(!$language) $language = self::current();
		
		if ( isset( self::$strings[$language][$string] ) )
		{
			// Translation string exists
			$string = self::$strings[$language][$string];
		}
		// else : Not found, not translated
		elseif ( $language !== REFERENCE_LANGUAGE )
		{
			# TODO : record this missing translation into database
		}

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

	private static function load($language)
	{
		// No need to load translation for strings written if reference language
		if ( $language !== REFERENCE_LANGUAGE )
		{
			$a = self::load_file(SYSTEM_DIR . "languages/$language.php", $language);
			$b = self::load_file(SITE_DIR . "languages/$language.php", $language);
			
			return $a || $b;
		}
		return TRUE;
	}

	/**
	 * Load a language file.
	 *
	 * @access	private
	 * @param	string $file		
	 * @param	string $language	
	 * @return	bool	True if the file was loaded
	 */
	private static function load_file($file, $language)
	{
		isset(self::$strings[$language]) or self::$strings[$language] = array();

		if(is_file($file)) {
			self::$strings[$language] += (array) require($file);
			return TRUE;
		}
		return FALSE;
	}
}