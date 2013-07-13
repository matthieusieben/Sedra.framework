<?php foreach((array) @$items as $value): ?>
	<?php if(is_array($value)): ?>
		<?php echo theme($value); ?>
	<?php else: ?>
		<?php echo ($value); ?>
	<?php endif ?>
<?php endforeach; ?>