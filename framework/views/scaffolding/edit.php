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
			<?php echo theme($form); ?>
		</div>

	</div>

	<?php require 'container_footer.php'; ?>

<?php require 'footer.php' ?>
