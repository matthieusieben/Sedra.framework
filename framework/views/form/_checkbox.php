<?php foreach($options as $_value => $_text): ?>
	<?php $_checked = is_array($value) ? in_array($_value, $value) : ($_value == $value); ?>
	<label class="<?php echo $attributes['type'] ?> <?php echo $style;?>">

		<?php $_attr = $attributes + array('value' => $_value); ?>
		<?php if ($_checked) $_attr['checked'] = 'checked'; ?>
		<input<?php echo attributes($_attr); ?>>

		<?php echo $_text ?>

	</label>
<?php endforeach; ?>
