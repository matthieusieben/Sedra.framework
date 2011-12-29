<?php if (DEVEL): ?>
	<?php debug(array(
		'included_files' => get_included_files(),
		'defined_functions' => get_defined_functions(),
		'defined_vars' => get_defined_vars(),
		'QUERIES' => class_exists('Database', FALSE) ? @Database::getLog('DEVEL') : NULL,
		'$_ENV' => $_ENV,
		'$_GET' => $_GET,
		'$_POST' => $_POST,
		'$_COOKIE' => $_COOKIE,
		'$_FILES' => $_FILES,
		'$_SERVER' => $_SERVER,
		'$_SESSION' => isset($_SESSION) ? $_SESSION : NULL,
		'$_REQUEST' => $_REQUEST,
		'$GLOBALS' => $GLOBALS,
		), t('Environment')); ?>
	<?php debug(round((microtime() - START_TIME) * 1000) . ' ms', t('Generation Time')); ?>
	<div id="debug">
		<?php if ($variables = debug()): ?>
			<?php foreach ($variables as $var): ?>
				<?php if ($var['message']): ?>
					<div class="dump-title"><?php echo $var['message']; ?></div>
				<?php endif ?>
				<?php dump($var['variable']); ?>
				<?php if ($var['backtrace']): ?>
					<div class="dump-backtrace"><?php p('Backtrace'); ?></div>
					<?php dump($var['backtrace']); ?>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		<?php echo "<!-- Execution time : <?php echo round((microtime() - START_TIME) * 1000) . ' ms'; ?> -->"; ?>
	</div>
<?php endif; ?>