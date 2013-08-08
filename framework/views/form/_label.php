<?php if ($label): ?>
	<label class="control-label" for="<?php if($type !== 'checkbox' || count($options) === 1) echo $id; ?>">
		<?php echo $label; ?>
	</label>
<?php endif ?>