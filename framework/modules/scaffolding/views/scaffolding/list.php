<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>
<?php require 'views/header.php' ?>

<div class="row-fluid">

	<div class="span2">
		<?php $tables_menu['attributes']['class'][] = 'nav-tabs'; ?>
		<?php $tables_menu['attributes']['class'][] = 'nav-stacked'; ?>
		<?php echo theme('components/menu', $tables_menu); ?>
	</div>

	<div class="span10">

		<?php if (empty($table_name)): ?>
			<p class="description">
				<?php echo t('Select a content table on the left.') ?>
			</p>
		<?php else: ?>

			<?php if (!empty($table_menu['items'])): ?>
				<?php $table_menu['attributes']['class'][] = 'nav-pills'; ?>
				<?php echo theme('components/menu', $table_menu); ?>
			<?php endif ?>

			<?php if (@$table_description): ?>
				<p class="description">
					<?php echo $table_description; ?>
				</p>
			<?php endif ?>

			<?php if (!empty($display_form)): ?>
				<?php echo theme($display_form); ?>
			<?php endif ?>

			<?php if (empty($table_content_table['rows'])): ?>
				<p class="text-warning">
					<?php echo t('No content to display.'); ?>
				</p>
			<?php else: ?>
				<?php $table_content_table['attributes']['class'][] = 'table-striped'; ?>
				<?php $table_content_table['attributes']['class'][] = 'table-ellipsis'; ?>
				<?php echo theme('components/table', $table_content_table); ?>
			<?php endif ?>

			<?php if (@$pagination): ?>
				<?php echo theme('components/pagination', $pagination) ?>
			<?php endif ?>

		<?php endif ?>
	</div>

</div>

<?php require 'views/footer.php' ?>
<?php require 'views/foot.php' ?>