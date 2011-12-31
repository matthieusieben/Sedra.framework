<div class="exception">
	<h2><?php echo $title . ' ('.$e->getCode().')'; ?></h2>

	<p class="message"><?php echo $e->getMessage(); ?></p>
	<p class="file"><?php p('File'); ?>: <?php echo $e->getFile(); ?></p>
	<p class="line"><?php p('Line'); ?>: <?php echo $e->getLine(); ?></p>

	<?php if (DEVEL): ?>
		<?php if ($e instanceof DatabaseException && isset($e->query)): ?>
			<h3><?php p('SQL Query'); ?></h3>
			<code class="sql"><pre><?php echo $e->query ?></pre></code>
		<?php else: ?>
			<h3><?php p('Trace'); ?></h3>
			<div class="trace"><?php dump($e->getTrace()); ?></div>
		<?php endif; ?>	
	<?php endif; ?>
</div>