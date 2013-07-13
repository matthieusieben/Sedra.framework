<?php

require_once 'framework/bootstrap.php';

global $controller;

$content = load_controller($controller);

if(!count(headers_list())) {
	set_status_header(200);
	header('Content-Type: text/html; charset=utf-8');
}

echo $content;