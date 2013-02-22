<?php require 'head.php'; ?>
<?php require 'header.php'; ?>

<div id="login">
	<div class="container">

		<div class="row">
			<div id="login" class="span6">
				<h2><?php echo t('Login'); ?></h2>
				<?php echo theme($login_form); ?>
			</div>

			<?php if($signup_form): ?>
				<div id="register" class="span6">
					<h2><?php echo t('Register'); ?></h2>
					<?php echo theme($signup_form); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php require 'footer.php'; ?>