<?php require 'head.php'; ?>
<?php require 'body.php'; ?>

<div class="container">
	<div class="row">
		<div class="span4 offset4">
			<?php require 'components/message.php' ?>

			<?php $signup_form['attributes']['class'][] = 'form-signup'; ?>
			<?php echo theme($signup_form); ?>
		</div>
	</div>
</div>

<?php require 'foot.php'; ?>