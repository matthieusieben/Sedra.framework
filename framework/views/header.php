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

	<header id="header" class="row">
		<?php if (@$site_name): ?>
			<h1 id="site_name" role="banner"><?php echo $site_name ?></h1>
		<?php endif ?>
		<?php echo theme('components/langmenu') ?>
	</header>

	<div class="row">
		<div class="span12">
			<?php require 'views/messages.php' ?>
		</div>
	</div>

	<div class="row">

		<section id="main" role="main" class="content <?php echo empty($aside) ? 'span12' : 'span9'; ?>">
			<?php echo theme('components/breadcrumb', $breadcrumb); ?>

			<?php if (@$title): ?>
				<h1 id="page_title"><?php echo $title ?></h1>
			<?php endif ?>
