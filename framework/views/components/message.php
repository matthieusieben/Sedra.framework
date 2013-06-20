<?php load_model('message'); ?>
<?php if(count($_m = message())): ?>
	<?php foreach($_m as $_type => $_messages): ?>
		<?php foreach($_messages as $_i => $_message): ?>
			<div class="alert <?php echo $_type; ?>">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?php echo $_message; ?>
			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
<?php endif; ?>