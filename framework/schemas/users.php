<?php

require_once 'includes/timezone.php';

# Requires
global $languages;
global $timezone_list;

return array(
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
			'default' => config('site.language', 'en'),
			'options' => $languages,
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
	'custom form handle' => '_schema_user_form_handle',
);

function _schema_user_form_handle(&$form, $action, $uid) {
	require_once 'includes/user.php';
	$form['fields']['pass']['type'] = 'password';
	if(form_run($form) && form_is_valid($form)) {
		$values = form_values($form);
		if($action == 'edit') {
			$account = user_find(array('uid' => $uid));
			foreach ($values as $key => $value)
				if($key !== 'pass' || !empty($value))
					$account->{$key} = $value;
			$account->status = 1;
			$account->save();
			return TRUE;
		} else {
			$values += array('status' => 1);
			$form['valid'] = user_register($values, $form['error']);
			return $form['valid'];
		}
	}
	return $form['submitted'];
}
