<?php

$config['controller'] = 'Home';
$config['language'] = 'en';
$config['timezone'] = 'Europe/Brussels';

$config['databases']['default']['default'] = array (
	'database' =>	'sedra_framework',
	'username' =>	'sedra',
	'password' =>	'sedra',
	'host' =>		'localhost',
	'port' =>		'',
	'driver' =>		'mysql',
	'prefix' =>		'',
);

# Is mod_rewrite activated on the server
$config['uri/rewrite'] = TRUE;
# Extension that any url should contain. If set, must start with a '.'.
$config['uri/extension'] = '.html';

$config['cookie/prefix'] = '';
$config['cookie/path'] = SCRIPT_PATH;
$config['cookie/domain'] = '.'.SERVER_NAME;
$config['cookie/secure'] = FALSE;
$config['cookie/validity'] = 86400; # 1209600 = 1 month / 86400 = 24 hours

# XSRF hash secret key. DO NOT use the example key.
$config['xsrf/key'] = 'QwErTyUiOp1234567890!';
$config['xsrf/stateful'] = TRUE;
$config['xsrf/timeout'] = 3600;

# Libraries to load at boot time
$config['autoload/libaries'] = array(
	'Krumo',
);

return $config;