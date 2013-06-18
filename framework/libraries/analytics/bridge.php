<?php

if(config('site.ga')) {
	hook_register('html_foot', function () {
		$account = config('site.ga');
		echo <<<EOS
<script>
	var _gaq=[['_setAccount','${account}'],['_trackPageview']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
EOS;
	});

	return TRUE;
}
else {
	return FALSE;
}
