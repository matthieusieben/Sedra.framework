<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>
<?php require 'views/header.php' ?>

	<?php require 'views/account/header.php' ?>

	<div id="account" class="row">

		<div id="edit" class="span8">
			<h3><?php echo t('Change my credentials') ?></h3>

			<?php if(@$edit_form): ?>
				<?php echo theme($edit_form) ?>
			<?php endif ?>
		</div>

		<div id="info" class="span4">
			<div class="box">
				<h3><?php echo t('Strong Passwords Are Key!') ?></h3>
				<p><?php echo t('You should use a strong password when securing your account. Your password should:<ul><li>Contain capital letters, numbers or symbols</li><li>Be something that you don\'t share with others</li><li>Be something hard to guess by others</li></ul>') ?></p>
			</div>
		</div>

	</div>

	<?php require 'views/account/footer.php' ?>

<?php require 'views/footer.php' ?>
<?php require 'views/foot.php' ?>