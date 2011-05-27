<?php $title = $e instanceof SedraException ? $e->getHeading() : t('Fatal error'); ?>
<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>

<h1><?php echo $title . ' ('.$e->getCode().')'; ?></h1>

<p><?php echo $e->getMessage(); ?></p>

<?php if (DEVEL && $e instanceof DatabaseException && isset($e->query) && Load::library('krumo')): ?>
	<h2>SQL Query</h2>
	<?php krumo($e->query); ?>
<?php endif ?>

<?php require 'views/foot.php' ?>