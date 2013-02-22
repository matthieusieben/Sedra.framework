<?php

define('DRUPAL_ROOT', FRAMEWORK_ROOT.'libraries/drupal/');

set_include_path(get_include_path().PATH_SEPARATOR.DRUPAL_ROOT);

function drupal_strtoupper($text) {
	return mb_strtoupper($text);
}

function drupal_strtolower($text) {
	return mb_strtolower($text);
}

function drupal_alter($type, &$data, &$context1 = NULL, &$context2 = NULL) {
	return;
}

require_once DRUPAL_ROOT.'includes/database/log.inc';
require_once DRUPAL_ROOT.'includes/database/database.inc';
