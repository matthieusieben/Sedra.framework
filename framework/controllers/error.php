<?php

switch($errno = val($arg[1])) {
case '401':
	return show_401();

case '403':
	return show_403();

case '404':
	return show_404();

default:
	throw new FrameworkException(t('Fatal error'), $errno);
}
