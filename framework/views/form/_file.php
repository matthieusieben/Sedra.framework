<?php $attributes['type'] = 'file'; ?>
<input<?php echo attributes($attributes); ?>>
<?php if($value): ?>
	<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
<?php endif; ?>
