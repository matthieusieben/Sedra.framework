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

function mail_send($options) {

	global $mailer;

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
		'date' => date('l j F Y, G:i', REQUEST_TIME),
		'id' => NULL,
	);
	$options += array(
		'replyto' => $options['from'],
		'returnpath' => $options['from'],
	);

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

	if($options['text'] && $options['html']) {
		$message->setBody($options['html'], 'text/html');
		$message->addPart($options['text'], 'text/plain');
	}
	else if($options['html']) {
		$message->setBody($options['body'], 'text/html');
	}
	else if($options['text']) {
		$message->setBody($options['text'], 'text/plain');
	}
	else {
		return FALSE;
	}

	foreach ((array) $options['attachments'] as $filename => $data) {
		$message->attach(Swift_Attachment::newInstance($data, $filename));
	}

	try {
		return $mailer->send($message);
	} catch (Exception $e) {
		if(config('devel')) {
			throw new FrameworkException($e);
		} else {
			require_once 'includes/log.php';
			log_exception($e);
			return FALSE;
		}
	}
}

return TRUE;