<?php $attributes['class'][] = "form-{$style}"; ?>
<form<?php echo attributes($attributes); ?>>
	<?php if ($title): ?>
		<h2 class="form-heading"><?php echo $title; ?></h2>
	<?php endif ?>
	<?php if(!$valid && $error): ?>
		<div class="control-group error">
			<span class="help-inline">
				<?php echo $error; ?>
			</span>
		</div>
	<?php endif; ?>
	<?php require 'views/form/fieldset.php'; ?>
</form>