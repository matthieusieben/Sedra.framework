<?php require 'head.php'; ?>
<?php require 'body.php'; ?>

<div class="container">
	<div class="row">
		<div class="span4 offset4">
			<?php require 'components/message.php' ?>

			<?php $login_form['attributes']['class'][] = 'form-signin'; ?>
			<?php echo theme($login_form); ?>
		</div>
	</div>
</div>

<?php require 'foot.php'; ?>