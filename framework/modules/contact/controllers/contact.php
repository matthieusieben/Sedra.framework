<?php

load_module('mail');

require_once 'includes/form.php';
require_once 'includes/menu.php';
require_once 'includes/theme.php';
require_once 'includes/message.php';
require_once 'includes/watchdog.php';

$contact_form = array(
	'fields' => array(
		'name' => array(
			'type' => 'text',
			'label' => t('Your name'),
			'required' => TRUE,
		),
		'mail' => array(
			'type' => 'email',
			'label' => t('Your email adress'),
			'required' => TRUE,
		),
		'subject' => array(
			'type' => 'text',
			'label' => t('Subject'),
			'required' => FALSE,
		),
		'message' => array(
			'type' => 'textarea',
			'label' => t('Your message'),
			'required' => TRUE,
		),
		'copy' => array(
			'type' => 'checkbox',
			'label' => t('Send youself a copy ?'),
			'multiple' => TRUE,
			'options' => array(
				TRUE => '',
			),
		),
		'watchdog' => array(
			'type' => 'watchdog',
			'required' => TRUE,
		),
		array(
			'type' => 'actions',
			'fields' => array(
				array(
					'type' => 'submit',
					'label' => t('Submit'),
				),
			),
		),
	),
);

if(form_run($contact_form) && form_is_valid($contact_form)) {
	$form_data = form_values($contact_form);

	$mail_data = array(
		'to' => config('site.email'),
		'from' => $form_data['mail'],
		'subject' => t('[@sitename] @subject', array(
			'@sitename' => config('site.name'),
			'@subject' => $form_data['subject'],
		)),
		'text' => $form_data['message'],
	);

	if(!empty($form_data['copy'])) {
		$mail_data['cc'] = $form_data['mail'];
	}

	if(mail_send($mail_data)) {
		message(MESSAGE_SUCCESS, t('Your message was sent.'));
	} else {
		message(MESSAGE_ERROR, t('An error occured. Please try again later.'));
	}
}

breadcrumb_add(array(
	'path' => 'contact',
	'title' => t('Contact'),
));

return theme('index', array(
	'title' => t('Contact'),
	'html' => theme($contact_form),
));
