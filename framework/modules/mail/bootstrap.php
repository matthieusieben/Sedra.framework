<?php

global $mailer;

function main_init() {
	global $mailer;
	if(!isset($mailer)) {
		require_once 'libraries/swiftmailer/lib/swift_required.php';

		switch (config('mail.method')) {
		case 'sendmail':
			$transport = Swift_SendmailTransport::newInstance(
				config('mail.sendmail', '/usr/sbin/sendmail -bs'));
			break;

		case 'smtp':
			$transport = Swift_SmtpTransport::newInstance(
				config('mail.smtp.server', 'localhost'),
				config('mail.smtp.port', 25),
				config('mail.smtp.security', NULL));

			if(config('mail.smtp.username'))
				$transport->setUsername(config('mail.smtp.username'));

			if(config('mail.smtp.password'))
				$transport->setPassword(config('mail.smtp.password'));

			break;

		case 'mail':
		default:
			$transport = Swift_MailTransport::newInstance();
			break;
		}

		$mailer = Swift_Mailer::newInstance($transport);
	}
	return $mailer;
}

function mail_send($options) {
	main_init();

	$options += array(
		'subject' => '',
		'from' => config('site.email'),
		'to' => NULL,
		'cc' => NULL,
		'bcc' => NULL,
		'html' => NULL,
		'text' => NULL,
		'priority' => 3,
		'attachments' => NULL,
		'date' => REQUEST_TIME,
		'id' => NULL,
	);
	$options += array(
		'replyto' => $options['from'],
		'returnpath' => $options['from'],
	);

	if(!$options['text'] && !$options['html'])
		return FALSE;

	$message = Swift_Message::newInstance()
		->setSubject($options['subject'])
		->setFrom($options['from'])
		->setReplyTo($options['replyto'])
		->setReturnPath($options['returnpath'])
		->setDate($options['date'])
		->setPriority($options['priority']);

	if($options['to'])
		$message->setTo($options['to']);

	if($options['cc'])
		$message->setCc($options['cc']);

	if($options['bcc'])
		$message->setBCc($options['bcc']);

	if($options['id'])
		$message->setID($options['Message-ID']);

	if($options['html'])
		$message->setBody($options['body'], 'text/html');

	if($options['text'])
		$message->setBody($options['text'], 'text/plain');

	foreach((array) $options['attachments'] as $filename => $data)
		$message->attach(Swift_Attachment::newInstance($data, $filename));

	try {
		global $mailer;
		return $mailer->send($message);
	} catch (Exception $e) {
		if(config('devel')) {
			throw new FrameworkException($e);
		} else {
			log_exception($e);
			return FALSE;
		}
	}
}

return TRUE;