<?php

load_model('menu');
load_model('schema');

global $schema;
global $content_table;
global $content_action;
global $content_id;
global $content_table_title;

$content_table = url_segment(1);
$content_action = url_segment(2, 'index');
$content_id = url_segment(3);
$content_table_title = t(@$schema[$content_table]['display name'] ? $schema[$content_table]['display name'] : $content_table);

if($content_table === 'index' && $content_action === 'index')
	$content_table = NULL;

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
	return load_controller('scaffolding/edit');
default:
	return load_controller("scaffolding/{$content_action}");
}
