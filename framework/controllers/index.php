<?php

load_model('theme');
load_model('data');

return theme('index', array(
	'view' => 'components/hero',
	'content' =>
		'<h2>'.data('site_name').'</h2>'.
		'<p>'.t('This is Sedra\'s default index page. Start making your own by making an <code>index.php</code> view file in the <code>application/views</code> folder.').'</p>',
));
