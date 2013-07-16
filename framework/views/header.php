<?php if(!empty($menus)): ?>
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
<?php endif ?>

<div id="wrapper" class="container">

	<header id="header">
		<?php if (@$site_name): ?>
			<h1 id="site_name" role="banner"><?php echo $site_name ?></h1>
		<?php endif ?>
	</header>

	<div id="main" role="main">
		<?php require 'views/components/message.php' ?>

		<?php echo theme('components/breadcrumb', $breadcrumb); ?>

		<?php if (@$title): ?>
			<h1 id="page_title"><?php echo $title ?></h1>
		<?php endif ?>
