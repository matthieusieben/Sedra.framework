<?php if (!empty($items)): ?>
	<?php $attributes = (array) @$attributes; ?>
	<?php if(empty($attributes['id'])): ?>
		<?php static $_carousel_id = 0; ?>
		<?php $attributes['id'] = '_carousel_'.($_carousel_id++); ?>
	<?php endif; ?>
	<?php $attributes['class'][] = 'carousel slide'; ?>
	<div <?php echo attributes($attributes); ?>>
		<?php if (@$indicators !== FALSE): ?>
			<ol class="carousel-indicators">
				<?php for ($_i=0; $_i < count($items); $_i++): ?>
					<li data-target="#<?php echo $attributes['id']; ?>" data-slide-to="<?php echo $_i; ?>"<?php if($_i == 0) echo ' class="active"' ?>></li>
				<?php endfor; ?>
			</ol>
		<?php endif ?>
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
			<a class="carousel-control left" href="#<?php echo $attributes['id']; ?>" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#<?php echo $attributes['id']; ?>" data-slide="next">&rsaquo;</a>
		<?php endif ?>
	</div>
<?php endif ?>
