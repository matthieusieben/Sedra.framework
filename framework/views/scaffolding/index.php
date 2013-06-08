<?php require 'head.php' ?>
<?php require 'header.php' ?>

	<?php require 'container_header.php'; ?>

	<div id="scaffolding" class="row">

		<div class="span2">
			<?php echo theme('components/menu', $tables_menu); ?>
		</div>

		<div class="span10">
			<p><?php echo t('Please select a table.'); ?></p>
		</div>

	</div>

	<?php require 'container_footer.php'; ?>

<?php require 'footer.php' ?>
