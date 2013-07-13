<?php require 'head.php' ?>
<?php require 'body.php' ?>
<?php require 'header.php' ?>

<div class="scaffolding scaffolding-table-<?php echo $table ?>">
	<?php foreach($schema['fields'] as $_field => $_field_info): ?>

		<?php if(@$_field_info['hidden'] === TRUE) continue; ?>

		<div class="<?php echo 'field-type-'.$_field ?>">

			<?php if(@$_field_info['show name'] !== FALSE): ?>
				<?php if(@$_field_info['display name']): ?>
					<span class="field-name"><?php echo $_field_info['display name'] ?></span>
				<?php endif ?>
			<?php endif ?>

			<div class="field-value">
				<?php switch(@$_field_info['view']): default: echo $item[$_field]; break; ?>
					<?php case 'date': ?>
						<?php echo theme_date($item[$_field]) ?>
					<?php break; ?>
					<?php case 'file': ?>
						<?php if($item[$_field]) echo theme_file($item[$_field]) ?>
					<?php break; ?>
					<?php case 'avatar': ?>
						<?php echo theme_avatar($item[$_field], 38) ?>
					<?php break; ?>
				<?php endswitch ?>
			</div>

		</div>

	<?php endforeach ?>
</div>

<?php require 'footer.php' ?>
<?php require 'foot.php' ?>