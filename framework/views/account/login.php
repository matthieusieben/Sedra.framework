<?php require 'head.php'; ?>
<?php require 'body.php'; ?>

<div class="container">
	<div class="row">
		<div class="span6 offset3">
			<?php require 'components/message.php' ?>

			<?php $login_form['attributes']['class'][] = 'form-signin'; ?>
			<?php $login_form['fields']['mail']['attributes']['class'][] = 'input-xlarge'; ?>
			<?php $login_form['fields']['pass']['attributes']['class'][] = 'input-xlarge'; ?>
			<?php $login_form['fields']['actions']['fields']['login']['attributes']['class'][] = 'btn-success'; ?>
			<?php echo theme($login_form); ?>
		</div>
	</div>
</div>

<?php require 'foot.php'; ?>