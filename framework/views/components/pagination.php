<?php $attributes = (array) @$attributes; ?>
<?php $attributes['class'][] = 'pagination'; ?>
<?php if(@$centered || (!@$right && !@$left)) $attributes['class'][] = 'pagination-centered'; ?>
<?php if(@$right && !@$left) $attributes['class'][] = 'pagination-right'; ?>
<div <?php echo attributes($attributes) ?>>
	<ul>
		<?php if (@$prev): ?>
			<li<?php echo attributes(@$prev['attributes']) ?>>
				<?php unset($prev['attributes']) ?>
				<?php echo l($prev + array('title' => '&laquo;')) ?>
			</li>
		<?php endif ?>
		<?php foreach ((array) @$items as $_item): ?>
			<li<?php echo attributes(@$_item['attributes']) ?>>
				<?php unset($_item['attributes']) ?>
				<?php echo l($_item) ?>
			</li>
		<?php endforeach ?>
		<?php if (@$next): ?>
			<li<?php echo attributes(@$next['attributes']) ?>>
				<?php unset($next['attributes']) ?>
				<?php echo l($next + array('title' => '&raquo;')) ?>
			</li>
		<?php endif ?>
	</ul>
</div>