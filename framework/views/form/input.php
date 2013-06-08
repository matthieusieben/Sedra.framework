<?php if($type == 'hidden'): ?>

	<?php $attributes += array('value' => $value); ?>
	<input <?php echo attributes($attributes); ?>>

<?php elseif($style == 'inline'): ?>

	<span class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<?php require '_input.php'; ?>
	</span>

<?php else: ?>

	<div class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<div class="controls">
			<?php require '_input.php'; ?>
			<?php require '_error.php'; ?>
			<?php require '_help.php'; ?>
		</div>
	</div>

<?php endif; ?>