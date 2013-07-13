<?php $attributes = (array) @$attributes; ?>
<?php $attributes['class'][] = 'navbar' ?>
<?php if (@$fixed): ?>
	<?php if (@$bottom): ?>
		<?php $attributes['class'][] = 'navbar-fixed-bottom' ?>
	<?php else: ?>
		<?php $attributes['class'][] = 'navbar-fixed-top' ?>
	<?php endif ?>
<?php elseif(@$top): ?>
	<?php $attributes['class'][] = 'navbar-static-top' ?>
<?php endif ?>
<?php if (isset($inverse)): ?>
	<?php $attributes['class'][] = 'navbar-inverse' ?>
<?php endif ?>
<div <?php echo attributes($attributes); ?>>
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<?php echo @$title ?>
			<div class="nav-collapse collapse">

				<?php if (!empty($main)): ?>
					<?php echo theme('components/menu', $main) ?>
				<?php endif ?>

				<?php if (!empty($secondary)): ?>
					<?php $secondary['attributes']['class'][] = 'pull-right' ?>
					<?php echo theme('components/menu', $secondary) ?>
				<?php endif ?>

			</div>
		</div>
	</div>
</div>