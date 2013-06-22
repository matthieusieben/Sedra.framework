<?php

global $content_table;
global $content_action;
global $content_id;

$content_table = url_segment(1);
$content_action = url_segment(2, 'index');
$content_id = url_segment(3);

switch($content_action) {
case 'add':
case 'edit':
case 'remove':
	return load_controller('scaffolding/edit');
default:
	return load_controller("scaffolding/{$content_action}");
}

