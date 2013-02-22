<?php if($prepend || $append): ?>
	<?php if($prepend) $_attr['class'][] = 'input-prepend'; ?>
	<?php if($append)  $_attr['class'][] = 'input-append'; ?>
	<div<?php echo attributes($_attr); ?>>
<?php endif; ?>

	<?php if($prepend): ?><span class="add-on"><?php echo $prepend; ?></span><?php endif; ?><?php
	?><select<?php echo attributes($attributes); ?>>
		<?php foreach($options as $_value => $_text): ?>
			<?php $_attr = array('value' => $_value); ?>
			<?php if (is_array($value) && in_array($_value, $value)) $_attr['selected'] = 'selected'; ?>
			<?php if ($_value == $value) $_attr['selected'] = 'selected'; ?>

			<option<?php echo attributes($_attr); ?>><?php echo $_text ?></option>

		<?php endforeach; ?>
	</select><?php
	?><?php if($append): ?><span class="add-on"><?php echo $append; ?></span><?php endif; ?>

<?php if($prepend || $append): ?>
	</div>
<?php endif; ?>