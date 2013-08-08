<?php

return array(
	'display name' => t('Files'),
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
		'view' => ADMINISTRATOR_RID,
		'list' => ADMINISTRATOR_RID,
		'add' => ADMINISTRATOR_RID,
		'edit' => ADMINISTRATOR_RID,
		'remove' => ADMINISTRATOR_RID,
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
	'custom form handle' => '_schema_files_form_handle',
);

function _schema_files_form_handle($table, $action, $id, &$form) {
	if($action === 'add' || $action === 'edit') {
		if(form_run($form) && form_is_valid($form)) {
			require_once 'includes/file.php';
			$values = form_values($form);
			return file_save($values['fid']);
		}
		return $form['submitted'];
	}
	else {
		return scaffolding_handle_form_default($table, $action, $id, $form);
	}
}