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
		<h2><?php p('Environement'); ?></h2>
		<code><pre><?php var_dump(array(
			'Execution time' => round((microtime() - START_TIME) * 1000) . ' ms',
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
			)); ?></pre></code>
	</div>
<?php endif ?>