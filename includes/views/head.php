<?php Load::helper('html'); ?>
<!DOCTYPE html>
<?php if (empty($lang)): ?>
	<html>
<?php else: ?>
	<html lang="<?php echo $lang; ?>">
<?php endif ?>
<head>
	<meta charset="UTF-8" />

	<title><?php echo $title; ?></title>

	<?php if (!empty($meta)): ?>
		<?php foreach($meta as $_name => $_content): ?>
			<meta name="<?php echo $_name; ?>" content="<?php echo $_content; ?>" />
		<?php endforeach; ?>
	<?php endif ?>

	<?php if ($_file = Url::file("images/favicon.ico")): ?>
		<link rel="shortcut icon" href="<?php echo $_file; ?>" />
		<?php /* This is the traditional favicon.
			 - size: 16x16 or 32x32
			 - transparency is OK
			 - see wikipedia for info on broswer support: http://mky.be/favicon/ */ ?>
	<?php endif ?>

	<?php if ($_file = Url::file("images/apple-touch-icon-precomposed.png")): ?>
		<link rel="apple-touch-icon" href="<?php echo Url::file("images/apple-touch-icon-precomposed.png"); ?>" />
		<?php /* The is the icon for iOS's Web Clip.
			 - size: 57x57 for older iPhones, 72x72 for iPads, 114x114 for iPhone4's retina display (IMHO, just go ahead and use the biggest one)
			 - To prevent iOS from applying its styles to the icon name it thusly: apple-touch-icon-precomposed.png
			 - Transparency is not recommended (iOS will put a black BG behind the icon) */ ?>
	<?php endif ?>

	<?php css('common.css'); ?>
	<?php css('style.css'); ?>

	<?php if(DEVEL): ?>
		<?php js('jquery.js'); ?>
	<?php else: ?>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
	<?php endif; ?>
