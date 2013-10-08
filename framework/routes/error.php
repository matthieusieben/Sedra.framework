<?php

$routes['error'] = array(
	'url' => 'error/:code',
	'controller' => 'error',
	'args' => array('code' => ':code'),
);