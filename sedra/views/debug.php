<?php if (DEVEL): ?>
	<?php Load::library('krumo'); ?>
	<div id="debug">
		<?php if ($variables = debug()): ?>
			<h2><?php p('Valiables'); ?></h2>
			<?php foreach ($variables as $var): ?>
				<?php if ($var['message']): ?>
					<div class="krumo-title"><?php echo $var['message']; ?></div>
				<?php endif ?>
				<?php krumo($var['variable']); ?>
				<?php if ($var['backtrace']): ?>
					<?php krumo($var['backtrace']); ?>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		<h2><?php p('Environement'); ?></h2>
		<?php krumo(array(
			'Execution time' => round((microtime() - START_TIME) * 1000) . ' ms',
			'QUERIES' => class_exists('Database') ? @Database::getLog('DEVEL') : NULL,
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
	</div>
<?php endif ?>