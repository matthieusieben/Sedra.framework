<?php require 'head.php' ?>
<?php require 'header.php' ?>
	<?php require 'container_header.php'; ?>
	<div class="row">

		<div class="span2">
			<?php $tables_menu['attributes']['class'][] = 'nav-tabs'; ?>
			<?php $tables_menu['attributes']['class'][] = 'nav-stacked'; ?>
			<?php echo theme('components/menu', $tables_menu); ?>
		</div>

		<div class="span10">

			<?php $table_menu['attributes']['class'][] = 'nav-pills'; ?>
			<?php echo theme('components/menu', $table_menu); ?>

			<?php if (@$table_description): ?>
				<p class="description">
					<?php echo $table_description; ?>
				</p>
			<?php endif ?>

			<?php foreach ($form['fields'] as &$field): ?>
				<?php $field['attributes']['class'][] = 'input-xxlarge'; ?>
			<?php endforeach ?>
			<?php echo theme($form); ?>

		</div>

	</div>
	<?php require 'container_footer.php'; ?>
<?php require 'footer.php' ?>
