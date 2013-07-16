<?php

require_once 'includes/form.php';
require_once 'includes/menu.php';
require_once 'includes/theme.php';
require_once 'includes/message.php';

user_role_required(AUTHENTICATED_RID);

global $user;

function check_current_password_field(&$form, &$field) {
	global $user;
	if(password_hash($field['value']) !== $user->pass) {
		$field['error'] = t('Wrong password.');
		return FALSE;
	}
	return TRUE;
}

function check_password_bis_field(&$form, &$field) {
	if ($field['required'] || $form['fields']['pass']['value']) {
		if ($field['value'] !== $form['fields']['pass']['value']) {
			$field['error'] = t('Paswords do not match.');
			return FALSE;
		}
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
			'callback' => '_form_callback_user_password',
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
				'update' => array(
					'type' => 'submit',
					'label' => t('Update'),
				),
				'delete' => array(
					'type' => 'submit',
					'label' => t('Delete my account'),
					'attributes' => array(
						'class' => array(
							'btn-danger',
						),
					),
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
		$delete = empty($values['update']) && !empty($values['delete']);
		if($delete) {
			db_delete('users')->condition('uid', $user->uid)->execute();
			$user = new AnonymousUser;
			session_regenerate();
			message(MESSAGE_SUCCESS, t('Your account has been removed.'));
			redirect(config('site.home', 'index'));
		}
		else {
			$user->save();
			user_setup_environment();
			message(MESSAGE_SUCCESS, t('Your account has been updated.'));
		}
	} catch(PDOException $e) {
		message(MESSAGE_ERROR, t('This email address is already in use.'));
	}
}

breadcrumb_add(array(
	'path' => 'account/index',
	'title' => t('My account'),
));

breadcrumb_add(array(
	'path' => 'account/credentials',
	'title' => t('Credentials'),
));

return theme('account/credentials', array(
	'title' => t('@username\'s account', array('@username' => $user->name)),
	'account' => $user,
	'edit_form' => $edit_form,
));
