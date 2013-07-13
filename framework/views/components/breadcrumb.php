<?php if(!isset($items)) return; ?>
<?php $attributes = (array) @$attributes; ?>
<?php $attributes['class'][] = 'breadcrumb'; ?>
<ul <?php echo attributes($attributes); ?>>
	<?php $_count = count($items); $_i = 0; ?>
	<?php foreach ($items as $_item): ?>
		<?php if (@$_item['path'] && url_is_active($_item['path'])) $_item['attributes']['class'][] = 'active'; ?>
		<li<?php echo attributes(array('class' => @$_item['attributes']['class'])); ?>>
			<?php if (!empty($_item['icon'])) $_item['title'] = $_item['icon'] . '&nbsp;' . $_item['title']; ?>
			<?php if (!empty($_item['badge'])) $_item['title'] .= '&nbsp;' . theme('components/badge', $_item['badge']); ?>
			<?php echo l($_item); ?>
			<?php if (++$_i < $_count): ?>
				<span class="divider">&gt;</span>
			<?php endif ?>
		</li>
	<?php endforeach; ?>
</ul>