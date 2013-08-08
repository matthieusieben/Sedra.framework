<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo t('You are being redirected.'); ?></title>
</head>
<body>
	<h1><?php echo t('You are being redirected.'); ?></h1>
	<p><?php echo t('If the page does not load automatically, please click !here.', array('!here' => '<a href="'.$url.'">'.t('here').'</a>')); ?></p>
</body>
</html>