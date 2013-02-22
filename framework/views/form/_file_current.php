<?php if($value): ?>
	<p class="help-block">
		<strong><?php echo t('Current file'); ?>&nbsp;:</strong>
		<?php echo theme_file($value); ?>
	</p>
<?php endif; ?>