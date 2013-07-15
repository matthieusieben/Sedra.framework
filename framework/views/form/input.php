<?php if($type == 'hidden'): ?>

	<?php $attributes += array('value' => $value); ?>
	<input <?php echo attributes($attributes); ?>>

<?php elseif($style == 'inline'): ?>

	<span class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require 'views/form/_label.php'; ?>
		<?php require 'views/form/_input.php'; ?>
	</span>

<?php else: ?>

	<div class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require 'views/form/_label.php'; ?>
		<div class="controls">
			<?php require 'views/form/_input.php'; ?>
			<?php require 'views/form/_error.php'; ?>
			<?php require 'views/form/_help.php'; ?>
		</div>
	</div>

<?php endif; ?>