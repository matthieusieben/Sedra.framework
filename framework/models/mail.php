<?php

require_once 'log.php';

load_library('swift');

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
		if(DEVEL) throw new FrameworkException($e);
		else log_exception($e);
		return FALSE;
	}
}
