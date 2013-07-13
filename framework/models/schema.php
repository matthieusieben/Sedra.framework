<?php

load_model('database');
load_model('timezone');

# Requires
global $timezone_list;
# Provides
global $schema;

$schema['users'] = array(
	'display name' => t('Users'),
	'fields' => array(
		'uid' => array(
			'type' => 'serial',
			'not null' => TRUE,
			'unsigned' => TRUE,
		),
		'rid' => array(
			'type' => 'enum',
			'not null' => TRUE,
			'default' => AUTHENTICATED_RID,
			'options' => array(
				AUTHENTICATED_RID => 'Simple user',
				MODERATOR_RID => 'Moderator',
				ADMINISTRATOR_RID => 'Administrator',
			),
			'display name' => t('Role'),
		),
		'mail' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('Email'),
		),
		'pass' => array(
			'type' => 'varchar',
			'not null' => FALSE,
			'length' => 40,
			'display name' => t('Password'),
		),
		'name' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('Name'),
		),
		'language' => array(
			'type' => 'varchar',
			'not null' => FALSE,
			'length' => 5,
		),
		'timezone' => array(
			'type' => 'enum',
			'not null' => FALSE,
			'options' => $timezone_list,
		),
		'data' => array(
			'type' => 'text',
			'not null' => FALSE,
			'size' => 'big',
			'default' => NULL,
			'hidden' => TRUE,
		),
		'created' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'hidden' => TRUE,
		),
		'access' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'hidden' => TRUE,
		),
		'login' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'hidden' => TRUE,
		),
		'status' => array(
			'type' => 'int',
			'not null' => TRUE,
			'unsigned' => TRUE,
			'default' => 0,
			'size' => 'tiny',
			'hidden' => TRUE,
		),
	),
	'primary key' => array('uid'),
	'unique keys' => array(
		'user_mail' => array('mail'),
	),
	'roles' => array(
		'view' => MODERATOR_RID,
		'list' => MODERATOR_RID,
		'add' => MODERATOR_RID,
		'edit' => MODERATOR_RID,
		'remove' => MODERATOR_RID,
	),
	'custom form handle' => function(&$form, $action, $uid) {
		load_model('user');
		if(form_run($form) && form_is_valid($form)) {
			$values = form_values($form);
			if($action == 'edit') {
				$account = user_find(array('uid' => $uid));
				foreach ($values as $key => $value)
					if($key !== 'pass' || !empty($value))
						$account->{$key} = $value;
				return $account->save();
			} else {
				return user_register($values);
			}
		}
		return FALSE;
	},
);

$schema['users_actions'] = array(
	'display name' => t('Users actions'),
	'description' => 'The table for user actions such as account activation or password reset.',
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

$schema['sessions'] = array(
	'display name' => t('Users sessions'),
	'description' => 'The table for user sessions.',
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

$schema['files'] = array(
	'display name' => t('Files'),
	'description' => 'The base table for storing files.',
	'fields' => array(
		'fid' => array(
			'type' => 'serial',
			'not null' => TRUE,
			'unsigned' => TRUE,
		),
		'uid' => array(
			'type' => 'int',
			'not null' => TRUE,
			'unsigned' => TRUE,
		),
		'hash' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 32,
		),
		'posted' => array(
			'type' => 'int',
			'not null' => TRUE,
			'unsigned' => TRUE,
		),
		'name' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 256,
		),
		'type' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 64,
		),
		'size' => array(
			'type' => 'int',
			'not null' => TRUE,
			'size' => 'big',
		),
		'content' => array(
			'type' => 'blob',
			'not null' => TRUE,
			'size' => 'big',
		),
		'tmp' => array(
			'type' => 'int',
			'not null' => TRUE,
			'size' => 'tiny',
			'default' => 1,
		),
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
			'cascade' => TRUE,
		),
	),
	'roles' => array(
		'view' => MODERATOR_RID,
		'list' => MODERATOR_RID,
		'add' => MODERATOR_RID,
		'edit' => MODERATOR_RID,
		'remove' => MODERATOR_RID,
	),
	# Scaffolding
	'custom form' => array(
		'fields' => array(
			'fid' => array(
				'label' => 'File',
				'type' => 'file',
				'required' => TRUE,
				'mime' => array('*'),
			),
		),
	),
	'custom form handle' => function(&$form) {
		load_model('form');
		load_model('file');
		if(form_run($form) && form_is_valid($form)) {
			$values = form_values($form);
			return file_save($values['fid']);
		}
		return FALSE;
	},
);

$schema['watchdog'] = array(
	'display name' => t('Watchdog'),
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

$schema['menus'] = array(
	'display name' => t('Menus'),
	'fields' => array(
		'menu' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('System name'),
		),
	),
	'primary key' => array('menu'),
	'roles' => array(
		'view' => ADMINISTRATOR_RID,
		'list' => ADMINISTRATOR_RID,
		'add' => ADMINISTRATOR_RID,
		'edit' => ADMINISTRATOR_RID,
		'remove' => ADMINISTRATOR_RID,
	),
);

$schema['menu_items'] = array(
	'display name' => t('Menu items'),
	'fields' => array(
		'mid' => array(
			'type' => 'serial',
			'not null' => TRUE,
			'unsigned' => TRUE,
			'hidden' => TRUE,
		),
		'menu' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('Menu'),
		),
		'parent' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'display name' => t('Parent item'),
		),
		'title' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('Item title'),
		),
		'role' => array(
			'type' => 'enum',
			'not null' => FALSE,
			'default' => -1,
			'options' => array(
				-1 => 'Anyone',
				ANONYMOUS_RID => 'Anonymous only',
				AUTHENTICATED_RID => 'Authenticated users',
				MODERATOR_RID => 'Moderators',
				ADMINISTRATOR_RID => 'Administrators',
			),
			'display name' => t('Required role'),
			'description' => t('Leave empty if you want this item to appear only when it has visible sub pages.')
		),
		'path' => array(
			'type' => 'varchar',
			'not null' => FALSE,
			'length' => 512,
			'display name' => t('Path'),
		),
		'weight' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'default' => 0,
			'display name' => t('Position'),
		),
	),
	'primary key' => array('mid'),
	'foreign keys' => array(
		'menu' => array(
			'table' => 'menus',
			'columns' => array('menu' => 'menu'),
			'cascade' => TRUE,
		),
		'parent_item' => array(
			'table' => 'menu_items',
			'columns' => array('parent' => 'mid'),
			'cascade' => FALSE,
		),
	),
	'roles' => array(
		'view' => MODERATOR_RID,
		'list' => MODERATOR_RID,
		'add' => MODERATOR_RID,
		'edit' => MODERATOR_RID,
		'remove' => MODERATOR_RID,
	),
	'order' => array(
		'menu' => 'ASC',
		'parent' => 'ASC',
		'weight' => 'ASC',
	),
);

# Add user defined schemas

foreach((array) scandir(APP_MODELS.'schemas') as $file)
	if($file[0] !== '.')
		require_once APP_MODELS.'schemas/'.$file;

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

	# Create table
	if(!empty($schema['custom sql'])) {
		try {
			db_query($schema['custom sql'])->execute();
		} catch (PDOException $e) {
			if(strpos($e->getMessage(), 'already exists') === FALSE)
				throw $e;
		}
	}
	else {
		foreach ($schema['fields'] as $name => &$definition) {
			switch(strtolower(@$definition['type'])) {
			case 'enum':
				if(isset($definition['default']))
					# convert to string
					$definition['default'] = "{$definition['default']}";
				$definition['mysql_type'] = 'ENUM('.implode(',', array_map(function($f) {return "'".strtr($f,"'","\'")."'";}, array_keys($definition['options']))).')';
				break;
			case 'datetime':
				$definition['mysql_type'] = 'DATETIME';
				break;
			default:
				continue;
			}
		}

		db_create_table($schema['name'], $schema);

		# Create foreign key constraints
		if (!empty($schema['foreign keys'])) {
			foreach ($schema['foreign keys'] as $constraint_name => $constraint) {

				$table_name = $schema['name'];
				$foreign_table = $constraint['table'];
				$action = @$constraint['cascade'] ? 'CASCADE' : 'SET NULL';
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
						FOREIGN KEY ( {$local_fields} ) REFERENCES {{$foreign_table}} ( {$foreign_fields} ) ON UPDATE {$action} ON DELETE {$action}
					");
				}
			}
		}
	}
}
