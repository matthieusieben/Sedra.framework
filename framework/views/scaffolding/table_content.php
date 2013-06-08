<?php require 'head.php' ?>
<?php require 'header.php' ?>
	<?php require 'container_header.php'; ?>
	<div id="scaffolding" class="row">

		<div class="span2">
			<?php echo theme('components/menu', $tables_menu); ?>
		</div>

		<div class="span10">

			<?php $scaffolding_menu['attributes']['class'][] = 'nav-pills'; ?>
			<?php echo theme('components/menu', $scaffolding_menu); ?>

			<?php if (!empty($display_form)): ?>
				<?php echo theme($display_form); ?>
			<?php endif ?>

			<?php if (empty($table_content_table['rows'])): ?>
				<p class="text-warning">
					<?php echo t('No content to display.'); ?>
				</p>
			<?php else: ?>
				<?php echo theme('components/table', $table_content_table); ?>
			<?php endif ?>

		</div>

	</div>
	<?php require 'container_footer.php'; ?>
<?php require 'footer.php' ?>
