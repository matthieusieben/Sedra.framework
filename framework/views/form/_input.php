<?php if($type == 'textarea'): ?>
	<div>
		<textarea<?php echo attributes($attributes); ?>><?php echo $value; ?></textarea>
	</div>
<?php else: ?>
	<?php $_attr = array(); ?>
	<?php if($prepend) $_attr['class'][] = 'input-prepend'; ?>
	<?php if($append)  $_attr['class'][] = 'input-append'; ?>
	<div<?php echo attributes($_attr); ?>>
		<?php if($prepend): ?><span class="add-on"><?php echo $prepend; ?></span><?php endif; ?><?php
		?><input<?php echo attributes($attributes); ?>><?php
		?><?php if($append): ?><span class="add-on"><?php echo $append; ?></span><?php endif; ?>

	</div>
<?php endif; ?>
