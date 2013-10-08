<?php

switch($errno = val($args['code'])) {
case '401':
	return show_401();

case '403':
	return show_403();

default:
case '404':
	return show_404();
}
