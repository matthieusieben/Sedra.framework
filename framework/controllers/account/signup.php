<?php

require_once 'user.php';
require_once 'data.php';
require_once 'theme.php';
require_once 'form.php';
require_once 'timezone.php';

global $home_url, $timezone_list, $user;

user_role_required(ANONYMOUS_RID);

if(!config('user.subscription'))
	show_403();

$signup_form = array(
	'style' => 'vertical',
	'title' => t('Sign up'),
	'fields' => array(
		'name' => array(
			'placeholder' => t('Name'),
			'type' => 'text',
			'required' => TRUE,
			'attributes' => array(
				'class' => array('input-xlarge'),
			),
		),
		'mail' => array(
			'placeholder' => t('Email'),
			'type' => 'email',
			'required' => TRUE,
			'callback' => 'check_email_field',
			'attributes' => array(
				'class' => array('input-xlarge'),
			),
		),
		'pass' => array(
			'placeholder' => t('Password'),
			'type' => 'password',
			'required' => TRUE,
			'callback' => 'check_password_field',
			'attributes' => array(
				'class' => array('input-xlarge'),
			),
		),
		'language' => array(
			'placeholder' => t('Language'),
			'type' => 'select',
			'default' => $user->language,
			'required' => TRUE,
			'options' => lang_list(),
			'attributes' => array(
				'class' => array('input-xlarge'),
			),
		),
		'timezone' => array(
			'placeholder' => t('Timezone'),
			'type' => 'select',
			'default' => config('date.timezone'),
			'required' => FALSE,
			'options' => $timezone_list,
			'attributes' => array(
				'class' => array('input-xlarge'),
			),
		),
		array(
			'type' => 'fieldset',
			'fields' => array(
				array(
					'type' => 'submit',
					'label' => t('Sign up'),
				),
				array(
					'view' => 'html',
					'html' => config('user.subscription') ?
						l(array('title'=>t('Log in'),'path'=>'account/login','attributes'=>array('class'=>array('btn')))):
						NULL
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
