<fieldset>
	<?php if($label): ?>
		<legend><?php echo $label; ?></legend>
	<?php endif; ?>
	<?php foreach($fields as $field): ?>
		<?php if (empty($field)): ?>
			<?php continue ?>
		<?php elseif (is_string($field)): ?>
			<?php echo $field ?>
		<?php elseif (is_array($field) && !empty($field['view'])): ?>
			<?php echo theme($field) ?>
		<?php endif ?>
	<?php endforeach; ?>
</fieldset>