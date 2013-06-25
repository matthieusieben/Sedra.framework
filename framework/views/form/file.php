<?php if($style == 'inline'): ?>
	<span class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<?php require '_file.php'; ?>
		<?php require '_file_current.php'; ?>
	</span>
<?php else: ?>
	<div class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require '_label.php'; ?>
		<div class="controls">
			<?php require '_file.php'; ?>
			<?php require '_error.php'; ?>
			<p class="help-block">
				<?php require '_file_current.php'; ?>
			</p>
			<p class="help-block">
				<?php require '_file_remove.php'; ?>
			</p>
			<?php require '_help.php'; ?>
		</div>
	</div>
<?php endif; ?>