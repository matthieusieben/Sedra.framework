<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>
<?php require 'views/header.php' ?>

<div class="row">

	<div class="span2">
		<?php $tables_menu['attributes']['class'][] = 'nav-tabs'; ?>
		<?php $tables_menu['attributes']['class'][] = 'nav-stacked'; ?>
		<?php echo theme('components/menu', $tables_menu); ?>
	</div>

	<div class="span10">
		<?php $table_menu['attributes']['class'][] = 'nav-pills'; ?>
		<?php echo theme('components/menu', $table_menu); ?>

		<?php foreach ($form['fields'] as &$field): ?>
			<?php switch(@$field['type']): default: continue; ?>
			<?php case 'textarea': ?>
			<?php case 'text': ?>
				<?php $field['attributes']['class'][] = 'input-xxlarge'; ?>
				<?php break; ?>
			<?php endswitch; ?>
		<?php endforeach ?>
		<?php echo theme($form); ?>
	</div>

</div>

<?php require 'views/footer.php' ?>
<?php require 'views/foot.php' ?>