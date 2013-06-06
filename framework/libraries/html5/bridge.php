<?php

hook_register('html_head', function () {
	echo <<<EOS
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
EOS;
});
