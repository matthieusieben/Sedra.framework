<?php require 'head.php' ?>
<?php require 'header.php' ?>

<div id="account" class="container">
	<div class="row">
		<div id="info" class="span6">
			<h1>
				<?php echo theme_avatar($account, 40); ?>
				<?php echo $account->name; ?>
			</h1>
			<h2><?php echo $account->mail; ?></h2>
		</div>
		<div id="edit" class="span6">
			<?php if(@$edit_form): ?>
				<?php echo theme($edit_form); ?>
			<?php endif; ?>
		</div>

	</div>
</div>

<?php require 'footer.php' ?>
