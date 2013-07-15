<?php if($style == 'inline'): ?>
	<span class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require 'views/form/_label.php'; ?>
		<?php require 'views/form/_file.php'; ?>
		<?php require 'views/form/_file_current.php'; ?>
	</span>
<?php else: ?>
	<div class="control-group<?php if(!$valid) echo ' error';?>">
		<?php require 'views/form/_label.php'; ?>
		<div class="controls">
			<?php require 'views/form/_file.php'; ?>
			<?php require 'views/form/_error.php'; ?>
			<p class="help-block">
				<?php require 'views/form/_file_current.php'; ?>
			</p>
			<p class="help-block">
				<?php require 'views/form/_file_remove.php'; ?>
			</p>
			<?php require 'views/form/_help.php'; ?>
		</div>
	</div>
<?php endif; ?>