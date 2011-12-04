<?php if (DEVEL): ?>
	<div id="debug">
		<?php if ($variables = debug()): ?>
			<h2><?php p('Variables'); ?></h2>
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
		<div class="dump-title"><?php p('Environment'); ?></div>
		<?php dump(array(
			'included_files' => get_included_files(),
			'defined_functions' => get_defined_functions(),
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
			)); ?>
		<div class="dump-title"><?php p('Execution Time'); ?></div>
		<?php dump(round((microtime() - START_TIME) * 1000) . ' ms'); ?>
	</div>
<?php endif ?>