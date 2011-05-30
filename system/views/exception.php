<?php $title = $e instanceof SedraException ? $e->getHeading() : t('Fatal error'); ?>
<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>

<h1><?php echo $title . ' ('.$e->getCode().')'; ?></h1>

<p class="message"><?php echo $e->getMessage(); ?></p>

<?php if (DEVEL && $e instanceof DatabaseException && isset($e->query)): ?>
	<h2><?php p('SQL Query'); ?></h2>
	<code class="sql"><pre><?php echo $e->query ?></pre></code>
<?php endif ?>

<?php require 'views/foot.php' ?>