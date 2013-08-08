<?php if(isset($__data['view']) && $__data['view'] !== $__view): ?>

	<?php echo theme($__data) ?>

<?php elseif(isset($html) && is_string($html)): ?>

	<?php echo $html ?>

<?php elseif(config('devel') === 'hard'): ?>

	<?php throw new FrameworkException(t('Unrenderable <code>html</code> view.'), 500); ?>

<?php elseif(config('devel')): ?>

	<h3><?php echo t('Warning, unrenderable content') ?>:</h3>
	<?php if(function_exists('devel')) devel($__data); else var_dump($__data); ?>
	<h4><?php echo t('Trace') ?></h4>
	<?php if(function_exists('devel')) devel(debug_backtrace()); else foreach(debug_backtrace() as $trace) var_dump($trace); ?>

<?php endif ?>