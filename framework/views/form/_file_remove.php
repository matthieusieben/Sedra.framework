<?php if (@$rm_name): ?>
	<label for="<?php echo $rm_name; ?>">
		<input type="checkbox" name="<?php echo $rm_name; ?>" id="<?php echo $rm_name; ?>" value="1">
		<?php echo t('Remove'); ?>
	</label>
<?php endif ?>