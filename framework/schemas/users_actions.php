<?php

return array(
	'display name' => t('Users actions'),
	'fields' => array(
		'uid' => array(
			'type' => 'int',
			'not null' => TRUE,
			'unsigned' => TRUE),
		'action' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 32),
		'salt' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 32),
		'time' => array(
			'type' => 'int',
			'not null' => TRUE),
	),
	'unique keys' => array(
		'user_action' => array('uid', 'action'),
		'unique_salt' => array('salt'),
	),
	'foreign keys' => array(
		'action_account' => array(
			'table' => 'users',
			'columns' => array('uid' => 'uid'),
			'cascade' => TRUE,
		),
	),
);