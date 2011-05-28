<?php

$config['databases']['default']['default'] = array (
	'database' =>	'sedra',
	'username' =>	'sedra',
	'password' =>	'sedra',
	'host' =>		'localhost',
	'port' =>		'',
	'driver' =>		'mysql',
	'prefix' =>		'',
);

$config['cookie/prefix'] = '';
$config['cookie/path'] = SCRIPT_PATH;
$config['cookie/domain'] = '.'.SERVER_NAME;
$config['cookie/secure'] = FALSE;
$config['cookie/validity'] = 86400; // 1209600 = 1 month / 86400 = 24 hours

$config['language/default'] = 'en';

$config['uri/rewrite'] = TRUE; // Is mod_rewrite activated on the server
$config['uri/extension'] = '.html'; // Must start with a '.'

$config['xsrf/key'] = 'QwErTyUiOp1234567890!'; // XSRF hash secret key. DO NOT use this example key.
$config['xsrf/stateful'] = TRUE;
$config['xsrf/timeout'] = 3600;
