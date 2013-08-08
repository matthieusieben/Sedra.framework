<?php

global $config;
global $databases;

# Is this website under development (TRUE) or production (FALSE) ?
$config['devel'] = FALSE;

# Website parameters
$config['site.home'] = 'index';
$config['site.name'] = 'My great website';
$config['site.email'] = 'admin@example.com';
$config['site.ga'] = NULL; // UA-XXXXX-X
$config['site.language'] = 'en';
$config['site.languages'] = array(
	'fr' => 'French',
	'en' => 'English',
);

$config['site.locales'] = array(
	'fr' => 'fr_FR.UTF-8',
	'en' => 'en_US.UTF-8',
);

# Date time parameters
$config['date.timezone'] = 'Europe/Brussels';
date_default_timezone_set($config['date.timezone']);

# Account parametes
$config['user.subscription'] = FALSE || $config['devel'];
$config['user.pwd.secure'] = FALSE && !$config['devel'];
$config['user.pwd.min'] = $config['devel'] ? 3 : 7;

# Enable URL rewriting. see .htaccess
$config['url.rewrite'] = 'pathauto'; # 'pathauto', 'query' or FALSE

# See Drupal's database api for more details
$databases['default']['default'] = array (
	'database' =>	'database',
	'username' =>	'username',
	'password' =>	'password',
	'host' =>		'localhost',
	'port' =>		'',
	'driver' =>		'mysql',
	'prefix' =>		'',
);

# Modules to load by default
$config['modules'] = array();
# Framework modules
if($config['devel'])
$config['modules']['devel'] = FALSE;
$config['modules']['contact'] = TRUE;
$config['modules']['scaffolding'] = TRUE;
$config['modules']['html5shim'] = FALSE;
$config['modules']['bootstrap'] = FALSE;
$config['modules']['analytics'] = FALSE;
# User modules

# reCaptcha
$config['recaptcha.public'] = NULL;
$config['recaptcha.private'] = NULL;

# Disable caching
$config['cache.disabled'] = $config['devel'];

# Watchdog default properties
$config['watchdog.timeout'] = 3600;
$config['watchdog.attempts'] = 3;
$config['watchdog.hardlimit'] = 30;

# Mail settings
$config['mail.method'] = 'mail'; # mail / sendmail / smtp
$config['mail.sendmail'] = '/usr/sbin/sendmail -bs';
$config['mail.smtp.server'] = 'localhost';
$config['mail.smtp.port'] = 25;
$config['mail.smtp.username'] = NULL;
$config['mail.smtp.password'] = NULL;
$config['mail.smtp.security'] = 'ssl'; # NULL / ssl / tls

# Error logging
$config['log.reporting'] = $config['devel'] ? E_ALL : E_ALL & ~(E_DEPRECATED | E_NOTICE);
$config['log.destination'] = PRIVATE_DIR . 'logs/'.date('Y-m-d').'.log';
error_reporting(config('log.reporting', E_ALL));

# User session parameters
$config['session.write_interval'] = 180;
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);
