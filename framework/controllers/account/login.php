<?php

require_once 'user.php';
require_once 'data.php';
require_once 'theme.php';
require_once 'form.php';
require_once 'log.php';
require_once 'timezone.php';

global $home_url, $timezone_list, $user;

user_role_required(ANONYMOUS_RID);

$login_form = array(
	'fields' => array(
		'mail' => array(
			'label' => t('Email'),
			'type' => 'email',
			'required' => TRUE,
		),
		'pass' => array(
			'label' => t('Password'),
			'type' => 'password',
			#'required' => TRUE,
		),
		array(
			'type' => 'actions',
			'fields' => array(
				'login' => array(
					'type' => 'submit',
					'label' => t('Log in'),
				),
				'reset' => array(
					'type' => 'submit',
					'label' => t('Password reset'),
				),
			),
		),
	),
);

$signup_form = !config('user.subscription') ? NULL : array(
	'fields' => array(
		'mail' => array(
			'label' => t('Email'),
			'type' => 'email',
			'required' => TRUE,
			'callback' => 'check_email_field',
		),
		'name' => array(
			'label' => t('Name'),
			'type' => 'text',
			'required' => TRUE,
		),
		'pass' => array(
			'label' => t('Password'),
			'type' => 'password',
			'required' => TRUE,
			'callback' => 'check_password_field',
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
			'default' => config('date.timezone'),
			'required' => FALSE,
			'options' => $timezone_list,
		),
		array(
			'type' => 'actions',
			'fields' => array(
				array(
					'type' => 'submit',
					'label' => t('Sign up'),
				),
			),
		),
	),
);

if(form_run($login_form) && form_is_valid($login_form)) {
	$values = form_values($login_form);

	$mail = $values['mail'];
	$pass = $values['pass'];
	$reset = !empty($values['reset']) && empty($values['login']);

	if($reset) {
		# Reset
		user_action_request($mail, 'reset');

		redirect(isset($_GET['redirect']) ? $_GET['redirect'] : config('site.home', 'index'));
	}
	else {
		# Login
		if(user_login($mail, $pass)) {
			redirect(isset($_GET['redirect']) ? $_GET['redirect'] : config('site.home', 'index'));
		} else {
			message(MESSAGE_ERROR, t('Wrong email or password.'));
		}
	}
}

if($signup_form && form_run($signup_form) && form_is_valid($signup_form)) {
	$values = form_values($signup_form);
	if(user_register($values)) {
		redirect(isset($_GET['redirect']) ? $_GET['redirect'] : config('site.home', 'index'));
	}
}

return theme('account/login', array(
	'login_form' => $login_form,
	'signup_form' => $signup_form,
));
