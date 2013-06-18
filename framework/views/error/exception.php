<?php require 'error/header.php' ?>

<?php $_code = $exception instanceof FrameworkException ? $exception->getCode() : 500; ?>

<h1><?php echo t('Error @code', array('@code' => $_code)); ?></h1>

<?php if (config('devel') || $_code < 500): ?>

	<p><?php echo $exception->getMessage(); ?></p>

<?php else: ?>

	<p><?php echo t('An internal error occured. Please try again later.'); ?></p>

<?php endif; ?>

<?php if(config('devel')): ?>

	<dl>
		<dt><?php echo t('File') ?></dt>
		<dd><code><?php echo $exception->getFile(); ?></code></dd>
		<dt><?php echo t('Line') ?></dt>
		<dd><code><?php echo $exception->getLine(); ?></code></dd>
	</dl>

	<h2><?php echo t('Backtrace'); ?></h2>
	<?php if(function_exists('kprintr')) kprintr($exception->getTrace()); else var_dump($exception->getTrace()); ?>

	<h2><?php echo t('Exception'); ?></h2>
	<?php if(function_exists('kprintr')) kprintr($exception); else var_dump($exception); ?>

	<h2><?php echo t('Output buffer content'); ?></h2>
	<?php if(!empty($output_buffer)): ?>
		<pre><?php if(function_exists('kprintr')) kprintr($output_buffer); else var_dump($output_buffer); ?></pre>
	<?php else: ?>
		<p><em><?php echo t('Empty'); ?></em></p>
	<?php endif; ?>

<?php endif; ?>

<?php require 'error/footer.php' ?>