<?php

load_model('user');
load_model('data');
load_model('theme');
load_model('form');
load_model('timezone');
load_model('watchdog');

if(user_has_role(AUTHENTICATED_RID))
	return redirect(config('site.home', 'account'));

if(!config('user.subscription'))
	show_403();

global $timezone_list;
global $user;

$signup_form = array(
	'style' => 'vertical',
	'title' => t('Sign up'),
	'fields' => array(
		'name' => array(
			'placeholder' => t('Name'),
			'type' => 'text',
			'required' => TRUE,
		),
		'mail' => array(
			'placeholder' => t('Email'),
			'type' => 'email',
			'required' => TRUE,
			'callback' => 'check_email_field',
		),
		'pass' => array(
			'placeholder' => t('Password'),
			'type' => 'password',
			'required' => TRUE,
			'callback' => '_form_callback_user_password',
		),
		'language' => array(
			'placeholder' => t('Language'),
			'type' => 'select',
			'default' => $user->language,
			'required' => TRUE,
			'options' => lang_list(),
		),
		'timezone' => array(
			'placeholder' => t('Timezone'),
			'type' => 'select',
			'default' => config('date.timezone'),
			'required' => FALSE,
			'options' => $timezone_list,
		),
		'watchdog' => array(
			'type' => 'watchdog',
			'required' => TRUE,
		),
		'actions' => array(
			'type' => 'fieldset',
			'fields' => array(
				'signup' => array(
					'type' => 'submit',
					'label' => t('Create my account'),
				),
				'login' => array(
					'view' => 'html',
					'html' => array(
						'title'=>t('Log in'),
						'path'=>'account/login',
					),
				),
			),
		),
	),
);

if(form_run($signup_form) && form_is_valid($signup_form)) {
	$values = form_values($signup_form);
	if(user_register($values)) {
		redirect(isset($_GET['redirect']) ? $_GET['redirect'] : config('site.home', 'index'));
	}
}

return theme('account/signup', array(
	'signup_form' => $signup_form,
));
