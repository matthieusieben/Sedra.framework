<?php

/**
 * Load the configuration file
 *
 * @return void
 */
function config_init()
{
	global $config;

	if( ! @include(SITE_DIR.'settings.php') )
		redirect(BASE_URL.'install.php');

	if( ! is_array($config) )
		fatal('Invalid configuration file.');

	$config = Hook::call(HOOK_CONFIG_INIT, $config);
}

/**
 * Get a configuration item from the settings file.
 *
 * @param string $k the language key name
 * @param string $d the default value if $k is missing, NULL by default
 * @return mixed
 */
function config( $item, $default = NULL )
{
	global $config;

	if( isset( $config[$item] ) )
	{
		return $config[$item];
	}
	else if( ! isset($config) )
	{
		fatal('Settings are not initialized.');
	}

	return $default;
}
