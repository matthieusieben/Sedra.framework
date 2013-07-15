<?php

require_once 'includes/data.php';
require_once 'includes/theme.php';

return theme('index', array(
	'view' => 'components/hero',
	'content' =>
		'<p>This is Sedra\'s default index page.<br>Start making your own by creating a new module in the <code>application</code> folder.</p>',
));
