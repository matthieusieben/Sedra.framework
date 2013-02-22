<?php

global $mailer;

require_once __DIR__.'/swiftmailer/lib/swift_required.php';

if(!isset($mailer)) {

	$transport = NULL;

	switch (config('mail.method', 'mail')) {

	case 'sendmail':
		$transport = Swift_SendmailTransport::newInstance(
			config('mail.sendmail', '/usr/sbin/sendmail -bs'));
		break;

	case 'smtp':
		$transport = Swift_SmtpTransport::newInstance(
			config('mail.smtp.server', 'localhost'),
			config('mail.smtp.port', 25),
			config('mail.smtp.security', NULL));

		if($_username = config('mail.smtp.username'))
			$transport->setUsername($_username);

		if($_password = config('mail.smtp.password'))
			$transport->setPassword($_password);

		unset($_username, $_password);

		break;

	case 'mail':
	default:
		$transport = Swift_MailTransport::newInstance();
		break;
	}

	$mailer = Swift_Mailer::newInstance($transport);
}
