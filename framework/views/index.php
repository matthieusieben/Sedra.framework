<?php require 'head.php'; ?>
<?php require 'header.php'; ?>

	<div class="hero-unit">

		<h1 id="site-title"><?php echo $site_name; ?></h1>

		<?php if($site_slogan): ?>
			<h2 id="site-slogan"><?php echo $site_slogan; ?></h2>
		<?php endif; ?>

		<p><?php echo t('This is Sedra\'s default index page. Start making your own by making an <code>index.php</code> view file in the <code>app/views</code> folder.'); ?></p>

	</div>

<?php require 'footer.php'; ?>