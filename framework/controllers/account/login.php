<?php

load_model('user');
load_model('theme');
load_model('form');
load_model('watchdog');

if(user_has_role(AUTHENTICATED_RID))
	return redirect(config('account'));

$login_form = array(
	'style' => 'vertical',
	'title' => config('site.name', 'Sedra Framework'),
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
				array(
					'view' => 'html',
					'html' => !config('user.subscription') ? NULL :
						l(array(
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
		if(user_login($mail, $pass)) {
			redirect(config('site.home', 'index'));
		} else {
			$login_form['valid'] = FALSE;
			$login_form['error'] = t('Wrong email or password.');
		}
	}
}

return theme('account/login', array(
	'login_form' => $login_form,
));
