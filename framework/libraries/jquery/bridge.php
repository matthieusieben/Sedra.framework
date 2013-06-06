<?php

hook_register('html_foot', function () {
	$file_url = file_url('framework/libraries/jquery/jquery-2.0.2.min.js');
	echo <<<EOS
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script>window.jQuery || document.write('<script src="${file_url}"><\/script>')</script>
EOS;
});
