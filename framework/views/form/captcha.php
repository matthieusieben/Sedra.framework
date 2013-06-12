<?php if($style == 'inline'): ?>

	<span class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<?php echo $captcha; ?>
	</span>

<?php else: ?>

	<div class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<div class="controls">
			<?php echo $captcha; ?>
			<?php require '_error.php'; ?>
			<?php require '_help.php'; ?>
		</div>
	</div>

<?php endif; ?>