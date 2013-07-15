<?php

return array(
	'fields' => array(
		'id' => array(
			'type' => 'varchar',
			'not null' => FALSE,
			'length' => 128),
		'count' => array(
			'type' => 'int',
			'not null' => TRUE,
			'unsigned' => TRUE,
			'size' => 'small', 'default' => 1),
		'hostname' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128),
		'timestamp' => array(
			'type' => 'int',
			'not null' => TRUE),
	),
	'unique keys' => array(
		'action' => array('hostname', 'id'),
	),
);
