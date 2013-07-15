<?php

require_once 'includes/user.php';
require_once 'includes/form.php';
require_once 'includes/menu.php';
require_once 'includes/theme.php';
require_once 'includes/timezone.php';
require_once 'includes/message.php';

user_role_required(AUTHENTICATED_RID);

global $timezone_list, $user;

$edit_form = array(
	'fields' => array(
		'name' => array(
			'label' => t('Name'),
			'type' => 'text',
			'default' => $user->name,
			'required' => TRUE,
		),
		'language' => array(
			'label' => t('Language'),
			'type' => 'select',
			'default' => $user->language,
			'required' => TRUE,
			'options' => language_list(),
		),
		'timezone' => array(
			'label' => t('Timezone'),
			'type' => 'select',
			'required' => TRUE,
			'options' => $timezone_list,
			'default' => $user->timezone,
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

	$user->name = $values['name'];
	$user->language = $values['language'];
	$user->timezone = $values['timezone'];

	try {
		$user->save();
		user_setup_environment();
		message(MESSAGE_SUCCESS, t('Your account has been updated.'));
	} catch(Exception $e) {
		message(MESSAGE_ERROR, t('An error occured. Please try again.'));
	}
}

breadcrumb_add(array(
	'path' => 'account/index',
	'title' => t('My account'),
));

return theme('account/index', array(
	'title' => t('@username\'s account', array('@username' => $user->name)),
	'account' => $user,
	'edit_form' => $edit_form,
));
