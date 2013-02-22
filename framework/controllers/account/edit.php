<?php

require_once 'user.php';
require_once 'form.php';
require_once 'theme.php';
require_once 'timezone.php';

user_role_required(AUTHENTICATED_RID);

global $home_url, $timezone_list, $user;

function check_password_bis_field(&$form, &$field) {
	if ($field['value'] !== $form['fields']['pass']['value']) {
		$field['error'] = t('Paswords do not match.');
		return FALSE;
	}
	return TRUE;
}

$edit_form = array(
	'fields' => array(
		'name' => array(
			'label' => t('Name'),
			'type' => 'text',
			'default' => $user->name,
			'required' => TRUE,
		),
		'pass' => array(
			'label' => t('Password'),
			'type' => 'password',
			'callback' => 'check_password_field',
		),
		'pass_check' => array(
			'label' => t('Password check'),
			'type' => 'password',
			'callback' => 'check_password_bis_field',
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

	if($values['pass_check'])
		$user->pass = $values['pass_check'];

	$user->save();

	user_setup_environment();

	message(MESSAGE_SUCCESS, t('Your account has been updated.'));
}

return theme('account/edit', array(
	'account' => $user,
	'edit_form' => $edit_form,
));
