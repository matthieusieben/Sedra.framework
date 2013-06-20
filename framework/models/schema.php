<?php

load_model('database');

# Core schemas

global $schema;

$schema['users'] = array(
	'description' => 'The base table for users.',
	'fields' => array(
		'uid'		=> array('type' => 'serial',	'not null' => TRUE,		'unsigned' => TRUE),
		'rid'		=> array('type' => 'int',		'not null' => TRUE,		'unsigned' => TRUE,	'default' => 3),
		'mail'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 128),
		'pass'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 40),
		'name'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 128),
		'language'	=> array('type' => 'varchar',	'not null' => FALSE,	'length' => 5),
		'timezone'	=> array('type' => 'varchar',	'not null' => FALSE,	'length' => 32),
		'data'		=> array('type' => 'text',		'not null' => FALSE,	'size' => 'big',	'default' => NULL),
		'created'	=> array('type' => 'int',		'not null' => FALSE,	'unsigned' => TRUE),
		'access'	=> array('type' => 'int',		'not null' => FALSE,	'unsigned' => TRUE),
		'login'		=> array('type' => 'int',		'not null' => FALSE,	'unsigned' => TRUE),
		'status'	=> array('type' => 'int',		'not null' => TRUE,		'unsigned' => TRUE,	'default' => 0,	'size' => 'tiny'),
	),
	'primary key' => array('uid'),
	'unique keys' => array(
		'user_mail' => array('mail'),
	),
);

$schema['users_actions'] = array(
	'description' => 'The table for user actions such as account activation or password reset.',
	'fields' => array(
		'uid'		=> array('type' => 'int',		'not null' => TRUE,		'unsigned' => TRUE),
		'action'	=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 32),
		'salt'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 32),
		'time'		=> array('type' => 'int',		'not null' => TRUE),
	),
	'unique keys' => array(
		'user_action' => array('uid', 'action'),
		'unique_salt' => array('salt'),
	),
	'foreign keys' => array(
		'action_account' => array(
			'table' => 'users',
			'columns' => array('uid' => 'uid'),
		),
	),
);

$schema['sessions'] = array(
	'description' => 'The table for user sessions.',
	'fields' => array(
		'sid'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 128),
		'uid'		=> array('type' => 'int',		'not null' => FALSE,	'unsigned' => TRUE),
		'hostname'	=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 128),
		'timestamp'	=> array('type' => 'int',		'not null' => TRUE,		'unsigned' => TRUE),
		'session'	=> array('type' => 'blob',		'not null' => FALSE,	'size' => 'big'),
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
		),
	),
);

$schema['files'] = array(
	'description' => 'The base table for storing files.',
	'fields' => array(
		'fid'		=> array('type' => 'serial',	'not null' => TRUE,		'unsigned' => TRUE),
		'uid'		=> array('type' => 'int',		'not null' => TRUE,		'unsigned' => TRUE),
		'hash'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 32),
		'posted'	=> array('type' => 'int',		'not null' => TRUE,		'unsigned' => TRUE),
		'name'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 256),
		'type'		=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 16),
		'size'		=> array('type' => 'int',		'not null' => TRUE,		'size' => 'big'),
		'content'	=> array('type' => 'blob',		'not null' => TRUE,		'size' => 'big'),
		'tmp'		=> array('type' => 'int',		'not null' => TRUE,		'size' => 'tiny',	'default' => 1),
	),
	'primary key' => array('fid'),
	'unique keys' => array(
		'file_reference' => array('hash'),
	),
	'indexes' => array(
		'uid' => array('uid'),
	),
	'foreign keys' => array(
		'file_owner' => array(
			'table' => 'users',
			'columns' => array('uid' => 'uid'),
		),
	),
);

$schema['watchdog'] = array(
	'fields' => array(
		'id'		=> array('type' => 'varchar',	'not null' => FALSE,	'length' => 128),
		'count'		=> array('type' => 'int',		'not null' => TRUE,		'unsigned' => TRUE,	'size' => 'small', 'default' => 1),
		'hostname'	=> array('type' => 'varchar',	'not null' => TRUE,		'length' => 128),
		'timestamp'	=> array('type' => 'int',		'not null' => TRUE),
	),
	'unique keys' => array(
		'action' => array('hostname', 'id'),
	),
);

# Schema installation functions

function sedra_schema_init($table_name, &$table) {

	if(empty($table['name']))
		$table['name'] = $table_name;

	$table += array(
		'mysql_suffix' => 'DEFAULT CHARACTER SET UTF8',
		'mysql_engine' => 'InnoDB',
	);
}

function sedra_schema_install($schema) {
	load_model('database');
	db_create_table($schema['name'], $schema);
	if (!empty($schema['foreign keys'])) {
		foreach ($schema['foreign keys'] as $constraint_name => $constraint) {

			$table_name = $schema['name'];
			$foreign_table = $constraint['table'];
			$local_fields = '';
			$foreign_fields = '';

			foreach((array) @$constraint['columns'] as $local => $foreign) {
				$local_fields = ltrim($local_fields.','.$local,',');
				$foreign_fields = ltrim($foreign_fields.','.$foreign,',');
			}

			if($table_name && $foreign_table && $local_fields && $foreign_fields) {
				db_query("
					ALTER TABLE {{$table_name}}
					ADD CONSTRAINT {{$constraint_name}}
					FOREIGN KEY ( $local_fields ) REFERENCES {{$foreign_table}} ( $foreign_fields ) ON UPDATE CASCADE ON DELETE CASCADE
				");
			}
		}
	}
}
