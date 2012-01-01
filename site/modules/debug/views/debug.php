<?php $_variables = debug(); ?>
<?php array_unshift($_variables, array(
	'message' => t('Included files'),
	'variable' => get_included_files(),
	)); ?>
<?php array_unshift($_variables, array(
	'message' => t('Environment'),
	'variable' => array(
		'$_ENV' => $_ENV,
		'$_GET' => $_GET,
		'$_POST' => $_POST,
		'$_COOKIE' => $_COOKIE,
		'$_FILES' => $_FILES,
		'$_SERVER' => $_SERVER,
		'$_SESSION' => isset($_SESSION) ? $_SESSION : NULL,
		'$_REQUEST' => $_REQUEST,
		'$GLOBALS' => $GLOBALS,
		)
	)); ?>
<div id="debug">
	<div id="debug-title">
		<?php p('Page generated in <b>@time</b> ms.', array('@time' => round((microtime() - START_TIME) * 1000))) ?>
	</div>
	<div id="debug-content">
		<?php foreach ($_variables as $_var): ?>
			<div class="dump">
				<?php if ($_var['message']): ?>
					<div class="dump-title"><?php echo $_var['message']; ?></div>
				<?php endif; ?>
				<div class="dump-content">
					<?php dump($_var['variable']); ?>
				</div>
			</div>
		<?php endforeach; ?>
		<?php if (class_exists('Database', FALSE)): ?>
			<?php $_queries = Database::getLog('DEVEL'); ?>
			<div id="debug-queries" class="dump">
				<div class="dump-title"><?php p('Queries'); ?></div>
				<div class="dump-content">
					<table>
						<thead>
							<tr>
								<th><?php p('ms'); ?></th>
								<th><?php p('where'); ?></th>
								<th><?php p('query'); ?></th>
								<th><?php p('target'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($_queries)): ?>
								<?php $_i = 0; ?>
								<?php foreach ($_queries as $_query): ?>
									<tr class="<?php echo (++$_i % 2 == 0) ? 'odd' : 'even'; ?>">
										<td><?php echo round($_query['time'] * 1000, 2); ?></td>
										<td>
										<?php $_where = !empty($_query['caller']['class']) ? $_query['caller']['class'] . '::' : ''; ?>
										<?php $_where .= $_query['caller']['function']; ?>
										<?php echo html($_where); ?></td>
										<td class="sql"><?php echo html($_query['query']); ?></td>
										<td><?php echo html($_query['target']); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="4"><?php p('No query.'); ?></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>