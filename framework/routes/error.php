<?php

$routes['error'] = array(
	'url' => 'error/(?<code>\w+)',
	'controller' => 'error',
	'args' => array('$code'),
);