<?php if (is_array(@$html)): ?>
	<?php echo theme($html); ?>
<?php else: ?>
	<?php echo @$html; ?>
<?php endif ?>