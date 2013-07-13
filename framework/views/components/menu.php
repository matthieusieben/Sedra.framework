<?php global $___mid; ?>
<?php if(empty($items)) return; ?>
<?php $attributes = (array) @$attributes; ?>
<?php if (!@$is_sub) $attributes['class'][] = 'nav'; ?>
<ul <?php echo attributes($attributes); ?>>
	<?php foreach ($items as $_item): ?>
		<?php if (@$_item['path'] && url_is_active($_item['path'])) $_item['attributes']['class'][] = 'active'; ?>
		<?php if (!empty($_item['items'])) {
			if(!@$is_sub) {
				$_item['attributes']['class'][] = 'dropdown';
				$_item['attributes']['class'][] = 'dropdown-toggle';
				$_item['attributes']['data-toggle'] = 'dropdown';
				$_item['title'] .= '<b class="caret"></b>';
			} else {
				$_item['attributes']['class'][] = 'dropdown-submenu';
			}
		} ?>
		<li<?php echo attributes(array('class' => @$_item['attributes']['class'])); ?>>
			<?php if (!empty($_item['icon'])) $_item['title'] = $_item['icon'] . '&nbsp;' . $_item['title']; ?>
			<?php if (!empty($_item['badge'])) $_item['title'] .= '&nbsp;' . theme('components/badge', $_item['badge']); ?>
			<?php echo l($_item); ?>
			<?php if (!empty($_item['items'])): ?>
				<?php echo theme('components/menu', array(
					'is_sub' => TRUE,
					'items' => $_item['items'],
					'attributes' => array('class' => array('dropdown-menu')),
				)); ?>
			<?php endif ?>
		</li>
	<?php endforeach; ?>
</ul>