<?php if(isset($__data['view']) && $__data['view'] !== $__view): ?>

	<?php echo theme($__data) ?>

<?php elseif(isset($html) && is_string($html)): ?>

	<?php echo $html ?>

<?php else: ?>

	<?php throw new FrameworkException(t('Unrenderable <code>html</code> view.'), 500); ?>

<?php endif ?>