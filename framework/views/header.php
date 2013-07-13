<?php echo theme('components/navbar', $menus + array(
	'fixed' => FALSE,
	'top' => TRUE,
	'title' => l(array(
		'title' => $site_name,
		'path' => 'index',
		'attributes' => array(
			'title' => $site_name,
			'class' => array('brand'),
	))),
)); ?>

<div id="container" class="container">

	<?php echo theme('components/breadcrumb', $breadcrumb); ?>

	<?php if (@$title): ?>
		<h2 id="title"><?php echo $title ?></h2>
	<?php endif ?>

	<?php require 'components/message.php' ?>
