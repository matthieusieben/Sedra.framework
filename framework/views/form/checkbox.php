<?php if($style == 'inline'): ?>
	<span class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<?php require '_checkbox.php'; ?>
	</span>
<?php else: ?>
	<div class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<div class="controls">
			<?php require '_checkbox.php'; ?>
			<?php require '_error.php'; ?>
			<?php require '_help.php'; ?>
		</div>
	</div>
<?php endif; ?>