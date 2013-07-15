<?php

return array(
	'fields' => array(
		'sid' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128),
		'uid' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE),
		'hostname' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128),
		'timestamp' => array(
			'type' => 'int',
			'not null' => TRUE,
			'unsigned' => TRUE),
		'session' => array(
			'type' => 'blob',
			'not null' => FALSE,
			'size' => 'big'),
	),
	'indexes' => array(
		'timestamp' => array('timestamp'),
		'uid' => array('uid'),
	),
	'primary key' => array('sid'),
	'foreign keys' => array(
		'session_account' => array(
			'table' => 'users',
			'columns' => array('uid' => 'uid'),
			'cascade' => TRUE,
		),
	),
);
