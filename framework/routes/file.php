<?php

$routes['file'] = array(
	'url' => 'file/:fileid',
	'controller' => 'file',
	'args' => array('fileid' => ':fileid'),
);

$routes['file_download'] = array(
	'url' => 'file/:fileid/download',
	'controller' => 'file',
	'args' => array('fileid' => ':fileid', 'download' => TRUE),
);
