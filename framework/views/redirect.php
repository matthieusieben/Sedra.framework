<?php require 'views/head.php' ?>
	<style>
	body { padding-top: 60px; }
	</style>
<?php require 'views/body.php' ?>

	<div class="container">
		<div class="hero-unit">

			<h1><?php echo t('You are being redirected.'); ?></h1>
			<p><?php echo t('If the page does not load automatically, please click !here.', array('!here' => '<a href="'.$url.'">'.t('here').'</a>')); ?></p>

		</div>
	</div>

<?php require 'views/foot.php' ?>
