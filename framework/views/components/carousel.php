<?php static $_carousel_id = 0; ?>
<?php $items = (array) @$items; ?>
<?php if (!empty($items)): ?>
	<?php $attributes = (array) @$attributes; ?>
	<?php $attributes += array('id' => '_carousel_'.($_carousel_id++), 'class' => array()); ?>
	<?php $attributes['class'] += array('carousel slide') ?>
	<?php $_target = $attributes['id']; ?>
	<div <?php echo attributes($attributes); ?>>
		<ol class="carousel-indicators">
			<?php for ($_i=0; $_i < count($items); $_i++): ?>
				<li data-target="#<?php echo $_target; ?>" data-slide-to="<?php echo $_i; ?>"<?php if($_i == 0) echo ' class="active"' ?>></li>
			<?php endfor; ?>
		</ol>
		<div class="carousel-inner">
			<?php foreach ($items as $_item): ?>
				<div class="item<?php if(@$_j++ == 0) echo ' active' ?>">
					<?php if (is_string($_item)): ?>
						<?php echo $_item; ?>
					<?php else: ?>
						<?php echo theme($_item); ?>
					<?php endif ?>
				</div>
			<?php endforeach ?>
		</div>
		<?php if (@$controls !== FALSE): ?>
			<a class="carousel-control left" href="#<?php echo $_target; ?>" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#<?php echo $_target; ?>" data-slide="next">&rsaquo;</a>
		<?php endif ?>
	</div>
<?php endif ?>
