<?php

$routes['file'] = array(
	'url' => 'file/(?<fileid>\w+)',
	'controller' => 'file',
	'args' => array('$fileid'),
);

$routes['file_download'] = array(
	'url' => 'file/(?<fileid>\w+)/download',
	'controller' => 'file',
	'args' => array('$fileid', 'download'),
);
