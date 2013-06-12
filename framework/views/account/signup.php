<?php require 'head.php'; ?>
<?php require 'body.php'; ?>

<div class="container">
	<div class="row">
		<div class="span6 offset3">
			<?php require 'components/message.php' ?>

			<?php $signup_form['attributes']['class'][] = 'form-signup'; ?>
			<?php $signup_form['fields']['name']['attributes']['class'][] = 'input-xlarge'; ?>
			<?php $signup_form['fields']['mail']['attributes']['class'][] = 'input-xlarge'; ?>
			<?php $signup_form['fields']['pass']['attributes']['class'][] = 'input-xlarge'; ?>
			<?php $signup_form['fields']['language']['attributes']['class'][] = 'input-xlarge'; ?>
			<?php $signup_form['fields']['timezone']['attributes']['class'][] = 'input-xlarge'; ?>
			<?php $signup_form['fields']['actions']['fields']['signup']['attributes']['class'][] = 'btn-success'; ?>
			<?php echo theme($signup_form); ?>
		</div>
	</div>
</div>

<?php require 'foot.php'; ?>