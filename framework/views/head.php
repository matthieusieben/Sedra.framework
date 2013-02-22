<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php if($_ = file_url('humans.txt')): ?>
		<link type="text/plain" rel="author" href="<?php echo $_; ?>" />
	<?php endif; ?>

	<?php if(DEVEL): ?>
		<?php echo theme_css('assets/css/bootstrap.css'); ?>
		<?php echo theme_css('assets/css/bootstrap-responsive.css'); ?>
	<?php else: ?>
		<?php echo theme_css('assets/css/bootstrap.min.css'); ?>
		<?php echo theme_css('assets/css/bootstrap-responsive.min.css'); ?>
	<?php endif; ?>

	<?php echo theme_css('assets/css/sedra.css'); ?>
	<?php echo theme_css('assets/css/custom.css'); ?>

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<link rel="shortcut icon" href="<?php echo file_url('assets/ico/favicon.ico'); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo file_url('assets/ico/apple-touch-icon-144-precomposed.png'); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo file_url('assets/ico/apple-touch-icon-114-precomposed.png'); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo file_url('assets/ico/apple-touch-icon-72-precomposed.png'); ?>">
	<link rel="apple-touch-icon-precomposed" href="<?php echo file_url('assets/ico/apple-touch-icon-57-precomposed.png'); ?>">

	<?php global $controller; ?>
	<?php echo theme_css('assets/css/'.$controller.'.css'); ?>

	<?php hook_invoke('html_head'); ?>
