<?php require 'views/head.php' ?>
	<style>
	body { padding-top: 60px; }
	</style>
<?php require 'views/body.php' ?>

	<div class="container">
		<div class="hero-unit">

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
				<?php if(function_exists('devel')) devel($exception->getTrace()); else var_dump($exception->getTrace()); ?>

				<h2><?php echo t('Exception'); ?></h2>
				<?php if(function_exists('devel')) devel($exception); else var_dump($exception); ?>

				<h2><?php echo t('Output buffer content'); ?></h2>
				<p>
					<?php if(!empty($output_buffer)): ?>
						<pre><?php if(function_exists('devel')) devel($output_buffer); else var_dump($output_buffer); ?></pre>
					<?php else: ?>
						<em><?php echo t('Empty'); ?></em>
					<?php endif; ?>
				</p>

			<?php endif; ?>

		</div>
	</div>

<?php require 'views/foot.php' ?>