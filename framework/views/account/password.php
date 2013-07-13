<?php require 'head.php' ?>
<?php require 'body.php' ?>
<?php require 'header.php' ?>

	<?php require 'account/header.php' ?>

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

	<?php require 'account/footer.php' ?>

<?php require 'footer.php' ?>
<?php require 'foot.php' ?>