<?php

require_once 'user.php';
require_once 'data.php';
require_once 'theme.php';
require_once 'form.php';
require_once 'message.php';

user_role_required(ANONYMOUS_RID);

$login_form = array(
	'style' => 'vertical',
	'title' => t('Log in'),
	'fields' => array(
		'mail' => array(
			'placeholder' => t('Email'),
			'type' => 'email',
			'required' => TRUE,
			'attributes' => array(
				'class' => array('input-xlarge'),
			),
		),
		'pass' => array(
			'placeholder' => t('Password'),
			'type' => 'password',
			'attributes' => array(
				'class' => array('input-xlarge'),
			),
		),
		array(
			'type' => 'fieldset',
			'fields' => array(
				'login' => array(
					'type' => 'submit',
					'label' => t('Log in'),
					'attributes' => array(
						'class' => array('btn-success'),
					),
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
		redirect(isset($_GET['redirect']) ? $_GET['redirect'] : config('site.home', 'index'));
	}
	else {
		# Login
		if(user_login($mail, $pass)) {
			redirect(isset($_GET['redirect']) ? $_GET['redirect'] : config('site.home', 'index'));
		} else {
			$login_form['valid'] = FALSE;
			$login_form['error'] = t('Wrong email or password.');
		}
	}
}

return theme('account/login', array(
	'login_form' => $login_form,
));
