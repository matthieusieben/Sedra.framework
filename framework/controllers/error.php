<?php

switch(url_segment(1)) {
case '403':
	return show_403();

case '404':
default:
	return show_404();
}
