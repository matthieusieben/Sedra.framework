<?php

defined('JQUERY') or define('JQUERY', 2);

hook_register('html_foot', function () {
	if(JQUERY === 2) {
		echo <<<EOS
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
EOS;
		if($jquery_2 = file_url('libraries/jquery/jquery-2.0.2.min.js'))
			echo <<<EOS
	<script>window.jQuery || document.write('<script src="${jquery_2}"><\/script>')</script>
EOS;
	} else {
		echo <<<EOS
	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
EOS;

		if($jquery_1 = file_url('libraries/jquery/jquery-1.10.1.min.js'))
			echo <<<EOS
	<script>window.jQuery || document.write('<script src="${jquery_1}"><\/script>')</script>
EOS;
		if($jquery_mig = file_url('libraries/jquery/jquery-migrate-1.2.1.min.js'))
			echo <<<EOS
	<script>window.jQuery || document.write('<script src="${jquery_mig}"><\/script>')</script>
EOS;
	}
});
