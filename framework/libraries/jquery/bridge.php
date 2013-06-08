<?php

defined('JQUERY') or define('JQUERY', 2);

hook_register('html_foot', function () {
	if(JQUERY === 2) {
		$file_url = file_url('framework/libraries/jquery/jquery-2.0.2.min.js');
		echo <<<EOS
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script>window.jQuery || document.write('<script src="${file_url}"><\/script>')</script>
EOS;
	} else {
		$file1_url = file_url('framework/libraries/jquery/jquery-1.10.1.min.js');
		$file2_url = file_url('framework/libraries/jquery/jquery-migrate-1.2.1.min.js');
		echo <<<EOS
	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script>window.jQuery || document.write('<script src="${file1_url}"><\/script><script src="${file2_url}"><\/script>')</script>
EOS;
	}
});
