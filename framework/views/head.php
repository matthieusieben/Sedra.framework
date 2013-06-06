<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php if($_ = file_url('humans.txt')): ?>
		<link type="text/plain" rel="author" href="<?php echo $_; ?>" />
	<?php endif; ?>

	<link rel="shortcut icon" href="<?php echo file_url('views/ico/favicon.ico'); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo file_url('views/ico/apple-touch-icon-144-precomposed.png'); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo file_url('views/ico/apple-touch-icon-114-precomposed.png'); ?>">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo file_url('views/ico/apple-touch-icon-72-precomposed.png'); ?>">
	<link rel="apple-touch-icon-precomposed" href="<?php echo file_url('views/ico/apple-touch-icon-57-precomposed.png'); ?>">

	<?php hook_invoke('html_head'); ?>

	<?php global $controller; ?>
	<?php echo theme_css('views/css/sedra.css'); ?>
	<?php echo theme_css('views/css/custom.css'); ?>
	<?php echo theme_css('views/css/'.$controller.'.css'); ?>
