<?php if (DEVEL): ?>
	<div id="debug">
		<?php if ($variables = debug()): ?>
			<h2><?php p('Valiables'); ?></h2>
			<?php foreach ($variables as $var): ?>
				<?php if ($var['message']): ?>
					<div class="var_dump"><?php echo $var['message']; ?></div>
				<?php endif ?>
				<code><pre><?php var_dump($var['variable']); ?></pre></code>
				<?php if ($var['backtrace']): ?>
					<code><pre><?php var_dump($var['backtrace']); ?></pre></code>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		<h2><?php p('Environment'); ?></h2>
		<code><pre><?php var_dump(array(
			'Execution time' => round((microtime() - START_TIME) * 1000) . ' ms',
			'QUERIES' => class_exists('Database', FALSE) ? @Database::getLog('DEVEL') : NULL,
			'included_files' => get_included_files(),
			'$_GET' => $_GET,
			'$_POST' => $_POST,
			'$_COOKIE' => $_COOKIE,
			'$_FILES' => $_FILES,
			'$_SESSION' => isset($_SESSION) ? $_SESSION : NULL,
			'$_REQUEST' => $_REQUEST,
			'$GLOBALS' => $GLOBALS,
			)); ?></pre></code>
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