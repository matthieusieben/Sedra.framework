<?php debug(array(
	'included_files' => get_included_files(),
	'defined_functions' => get_defined_functions(),
	'QUERIES' => (DEVEL AND class_exists('Database', FALSE)) ? @Database::getLog('DEVEL') : NULL,
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
	<?php if ($_variables = debug()): ?>
		<?php foreach ($_variables as $_var): ?>
			<?php if ($_var['message']): ?>
				<div class="dump-title"><?php echo $_var['message']; ?></div>
			<?php endif ?>
			<?php dump($_var['variable']); ?>
			<?php if ($_var['backtrace']): ?>
				<div class="dump-backtrace"><?php p('Backtrace'); ?></div>
				<?php dump($_var['backtrace']); ?>
			<?php endif ?>
		<?php endforeach ?>
	<?php endif ?>
</div>