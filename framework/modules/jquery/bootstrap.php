<?php

defined('JQUERY') or define('JQUERY', 2);

hook_register('html_foot', function () {
	if(JQUERY === 2) {
		echo <<<EOS
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
EOS;
	} else {
		echo <<<EOS
	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
EOS;
	}
});
