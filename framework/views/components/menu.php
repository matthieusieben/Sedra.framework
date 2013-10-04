<?php if (empty($items)) return; ?>
<?php $attributes = (array) @$attributes; ?>
<?php if (!@$is_sub) $attributes['class'][] = 'nav'; ?>
<ul <?php echo attributes($attributes); ?>>
	<?php foreach ($items as $_item): ?>
		<?php if (empty($_item)) continue; ?>
		<?php if (!array_key_exists('active', $_item) && @$_item['path'] && url_is_active($_item['path'])) $_item['attributes']['class'][] = 'active'; ?>
		<?php if (array_key_exists('active', $_item)) $_item['attributes']['class'][] = 'active'; ?>
		<?php if (!empty($_item['items']) && @$is_sub) $_item['attributes']['class'][] = 'dropdown-submenu'; ?>
		<?php if (!empty($_item['items']) && !@$is_sub) {
			$_item['attributes']['class'][] = 'dropdown';
			$_item['attributes']['class'][] = 'dropdown-toggle';
			$_item['attributes']['data-toggle'] = 'dropdown';
			$_item['title'] .= '<b class="caret"></b>';
		} ?>
		<li<?php echo attributes(array('class' => @$_item['attributes']['class'])); ?>>
			<?php if (!empty($_item['icon'])) $_item['title'] = $_item['icon'] . '&nbsp;' . $_item['title']; ?>
			<?php if (!empty($_item['badge'])) $_item['title'] .= '&nbsp;' . theme('components/badge', $_item['badge']); ?>
			<?php echo l($_item) ?>
			<?php if (!empty($_item['items'])): ?>
				<?php if (is_array($_item['items'])): ?>
					<?php echo theme('components/menu', array(
						'is_sub' => TRUE,
						'items' => $_item['items'],
						'attributes' => array('class' => array('dropdown-menu')),
					)) ?>
				<?php else: ?>
					<div class="dropdown-menu">
						<?php echo $_item['items'] ?>
					</div>
				<?php endif ?>
			<?php endif ?>
		</li>
	<?php endforeach; ?>
</ul>