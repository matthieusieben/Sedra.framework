<?php

require_once 'libraries/recaptcha-php/recaptchalib.php';

function _form_handle_captcha(&$form, &$field) {
	$field += array(
		'required' => TRUE,
	);
	global $request_protocol;
	$field['view'] = 'form/captcha';
	$field['captcha'] = recaptcha_get_html(
		config('recaptcha.public'),
		NULL,
		$request_protocol === 'https'
	);
}

function _form_callback_captcha(&$form, &$field) {
	$resp = recaptcha_check_answer(
		config('recaptcha.private'),
		ip_address(),
		@$_POST['recaptcha_challenge_field'],
		@$_POST['recaptcha_response_field']);

	if(!$resp->is_valid) {
		$field['error'] = t('Invalid captcha.');
	}

	return $field['required'] ? $resp->is_valid : TRUE;
}

if(!config('recaptcha.public') || !config('recaptcha.private'))
	throw new FrameworkException(t('Please configure Captcha public and private keys in your settings file.'), 500);