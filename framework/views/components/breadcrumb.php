<?php if(empty($items)) return; ?>
<?php if(!isset($divider)) $divider = '/' ?>
<?php $attributes = (array) @$attributes; ?>
<?php $attributes['class'][] = 'breadcrumb'; ?>
<ul <?php echo attributes($attributes); ?>>
	<?php $_count = count($items); $_i = 0; ?>
	<?php foreach ($items as $_item): ?>
		<?php if (@$_item['path'] && url_is_current($_item['path'])) $_item['attributes']['class'][] = 'active'; ?>
		<li<?php echo attributes(array('class' => @$_item['attributes']['class'])); ?>>
			<?php if (!empty($_item['icon'])) $_item['title'] = $_item['icon'] . '&nbsp;' . $_item['title']; ?>
			<?php if (!empty($_item['badge'])) $_item['title'] .= '&nbsp;' . theme('components/badge', $_item['badge']); ?>
			<?php if (url_is_current($_item['path'])) $_item['disabled'] = TRUE ?>
			<?php echo l($_item); ?>
			<?php if (++$_i < $_count): ?>
				<?php if ($divider): ?>
					<span class="divider"><?php echo $divider ?></span>
				<?php endif ?>
			<?php endif ?>
		</li>
	<?php endforeach; ?>
</ul>