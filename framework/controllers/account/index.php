<?php

require_once 'user.php';
require_once 'form.php';
require_once 'theme.php';
require_once 'timezone.php';

user_role_required(AUTHENTICATED_RID);

global $home_url, $timezone_list, $user;

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
			'options' => lang_list(),
		),
		'timezone' => array(
			'label' => t('Timezone'),
			'type' => 'select',
			'required' => FALSE,
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

return theme('account/index', array(
	'title' => t('@username\'s account', array('@username' => $user->name)),
	'account' => $user,
	'edit_form' => $edit_form,
));
