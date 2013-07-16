<?php

require_once 'includes/menu.php';
require_once 'includes/schema.php';
require_once 'includes/scaffolding.php';

global $content_table_info;
global $content_table;
global $content_action;
global $content_id;
global $content_table_title;

$content_table = val($arg[1]);
$content_action = val($arg[2], 'index');
$content_id = val($arg[3]);

if($content_table === 'index' && $content_action === 'index') {
	$content_table = NULL;
}

if($content_table) {
	$content_table_info = schema_get($content_table);
	$content_table_title = t(@$content_table_info['display name'] ? $content_table_info['display name'] : $content_table);
}
else {
	$content_table_info = NULL;
	$content_table_title = NULL;
}

breadcrumb_add(array(
	'path' => 'scaffolding',
	'title' => t('Site content'),
));

if($content_table && $content_table_title)
breadcrumb_add(array(
	'path' => 'scaffolding/'.$content_table,
	'title' => $content_table_title,
));

switch($content_action) {
case 'add':
case 'edit':
case 'remove':
	return load_controller('scaffolding/edit', $arg);
default:
	return load_controller("scaffolding/{$content_action}", $arg);
}
