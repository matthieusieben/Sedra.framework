<?php

require_once 'includes/theme.php';
require_once 'includes/form.php';
require_once 'includes/watchdog.php';

if(user_has_role(AUTHENTICATED_RID))
	return redirect(config('account'));

$login_form = array(
	'style' => 'vertical',
	'title' => config('site.name', 'Sedra Framework'),
	'autocomplete' => TRUE,
	'fields' => array(
		'mail' => array(
			'placeholder' => t('Email'),
			'type' => 'email',
			'required' => TRUE,
		),
		'pass' => array(
			'placeholder' => t('Password'),
			'type' => 'password',
			'required' => TRUE,
		),
		'watchdog' => array(
			'type' => 'watchdog',
			'required' => TRUE,
		),
		'actions' => array(
			'type' => 'fieldset',
			'fields' => array(
				'login' => array(
					'type' => 'submit',
					'label' => t('Log in'),
				),
				'reset' => array(
					'type' => 'submit',
					'label' => t('Reset'),
				),
				!config('user.subscription') ? NULL : array(
					'view' => 'html',
					'html' => l(array(
						'title'=>t('Sign up'),
						'path'=>'account/signup',
					)),
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
		redirect(config('site.home', 'index'));
	}
	else {
		# Login
		$login_form['valid'] = user_login($mail, $pass, 'login', $login_form['error']);
		if($login_form['valid']) {
			redirect(config('site.home', 'index'));
		}
	}
}

return theme('account/login', array(
	'login_form' => $login_form,
));
