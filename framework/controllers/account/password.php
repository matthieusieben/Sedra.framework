<?php

require_once 'user.php';
require_once 'form.php';
require_once 'theme.php';

user_role_required(AUTHENTICATED_RID);

global $home_url, $timezone_list, $user;

function check_current_password_field(&$form, &$field) {
	global $user;
	if(user_hash_password($field['value']) !== $user->pass) {
		$field['error'] = t('Incorrect password.');
		return FALSE;
	}
	return TRUE;
}

function check_password_bis_field(&$form, &$field) {
	if ($field['value'] !== $form['fields']['pass']['value']) {
		$field['error'] = t('Paswords do not match.');
		return FALSE;
	}
	return TRUE;
}

$edit_form = array(
	'fields' => array(
		'curr_pass' => array(
			'label' => t('Current password'),
			'type' => 'password',
			'callback' => 'check_current_password_field',
		),
		'pass' => array(
			'label' => t('New password'),
			'type' => 'password',
			'callback' => 'check_password_field',
		),
		'pass_check' => array(
			'label' => t('Confirm password'),
			'type' => 'password',
			'callback' => 'check_password_bis_field',
		),
		'mail' => array(
			'label' => t('Email'),
			'type' => 'email',
			'default' => $user->mail,
		),
		array(
			'type' => 'actions',
			'fields' => array(
				array(
					'type' => 'submit',
					'label' => t('Update'),
				),
			),
		),
	),
);

if(form_run($edit_form) && form_is_valid($edit_form)) {
	$values = form_values($edit_form);

	if($values['mail'])
		$user->mail = $values['mail'];

	if($values['pass_check'])
		$user->pass = $values['pass_check'];

	try {
		$user->save();
		user_setup_environment();
		message(MESSAGE_SUCCESS, t('Your account has been updated.'));
	} catch(Exception $e) {
		message(MESSAGE_ERROR, t('This email address is already in use.'));
	}
}

return theme('account/password', array(
	'title' => t('@username\'s account', array('@username' => $user->name)),
	'account' => $user,
	'edit_form' => $edit_form,
));
