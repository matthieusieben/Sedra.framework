<?php

if(!load_module('jquery', FALSE))
	return FALSE;

hook_register('html_head', function () {
	echo '<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">';
});

hook_register('html_foot', function () {
	echo '<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>';
});
