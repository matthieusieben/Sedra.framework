<?php if (DEVEL): ?>
	<div id="debug">
		<?php if ($variables = debug()): ?>
			<h2><?php p('Valiables'); ?></h2>
			<?php foreach ($variables as $var): ?>
				<?php if ($var['message']): ?>
					<div class="dump-title"><?php echo $var['message']; ?></div>
				<?php endif ?>
				<?php dump($var['variable']); ?>
				<?php if ($var['backtrace']): ?>
					<?php dump($var['backtrace']); ?>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		<h2><?php p('Environment'); ?></h2>
		<?php dump(array(
			'QUERIES' => class_exists('Database', FALSE) ? @Database::getLog('DEVEL') : NULL,
			'included_files' => get_included_files(),
			'defined_functions' => get_defined_functions(),
			'$_ENV' => $_ENV,
			'$_GET' => $_GET,
			'$_POST' => $_POST,
			'$_COOKIE' => $_COOKIE,
			'$_FILES' => $_FILES,
			'$_SERVER' => $_SERVER,
			'$_SESSION' => isset($_SESSION) ? $_SESSION : NULL,
			'$_REQUEST' => $_REQUEST,
			'$GLOBALS' => $GLOBALS,
			)); ?>
		<h2><?php p('Memory Usage'); ?></h2>
<pre>
<?php echo number_format(memory_get_usage() - START_MEMORY_USAGE); ?> <?php p('bytes'); ?> 
<?php echo number_format(memory_get_usage()); ?> <?php p('bytes'); ?> (<?php p('process'); ?>)
<?php echo number_format(memory_get_peak_usage(TRUE)); ?> <?php p('bytes'); ?> (<?php p('process peak'); ?>)
</pre>
		<h2><?php p('Execution Time'); ?></h2>
		<pre><?php print round((microtime() - START_TIME) * 1000); ?> <?php p('ms'); ?></pre>
	</div>
<?php endif ?>