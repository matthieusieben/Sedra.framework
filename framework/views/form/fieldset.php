<fieldset>
	<?php if($label): ?>
		<legend><?php echo $label; ?></legend>
	<?php endif; ?>
	<?php foreach($fields as $field): ?>
		<?php if (is_string($field)): ?>
			<?php echo $field; ?>
		<?php elseif (isset($field['view'])): ?>
			<?php echo theme($field); ?>
		<?php endif ?>
	<?php endforeach; ?>
</fieldset>