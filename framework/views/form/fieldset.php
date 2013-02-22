<fieldset>
	<?php if($label): ?>
		<legend><?php echo $label; ?></legend>
	<?php endif; ?>
	<?php foreach($fields as $field): ?>
		<?php echo theme($field); ?>
	<?php endforeach; ?>
</fieldset>