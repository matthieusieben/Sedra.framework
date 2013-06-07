<?php require 'head.php' ?>
<?php require 'header.php' ?>

	<?php require 'account/header.php'; ?>

	<div id="account" class="row">

		<div id="edit" class="span8">
			<h3><?php echo t('Account details'); ?></h3>
			<p><?php echo t('Make sure your account info is up to date and accurate. This is how we get in touch with you! We will never give out your email address or info to anyone.'); ?></p>
			<?php if(@$edit_form): ?>
				<?php echo theme($edit_form); ?>
			<?php endif; ?>
		</div>

		<div id="info" class="span4">
			<div class="box">
				<h3><?php echo t('Change Your Avatar?') ?></h3>
				<p><?php echo t('If you would like to add or change your avatar for your account, you can upload your own image at !gravatar using the same email address registered with this website.', array('!gravatar' => l(array('title'=>'Gravatar.com', 'uri'=>'http://gravatar.com', 'target' => 'blank')))); ?></p>
			</div>
		</div>

	</div>

	<?php require 'account/footer.php'; ?>

<?php require 'footer.php' ?>
