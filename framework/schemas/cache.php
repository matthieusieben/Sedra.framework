<?php

return array(
	'fields' => array(
		'id' => array(
			'type' => 'varchar',
			'length' => 64,
			'not null' => TRUE,
		),
		'content' => array(
			'type' => 'blob',
			'size' => 'big',
			'not null' => FALSE,
		),
	),
	'primary key' => array('id'),
);