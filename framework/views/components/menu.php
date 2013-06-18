<?php $attributes = (array) @$attributes; ?>
<?php if (!@$is_sub) $attributes['class'][] = 'nav'; ?>
<ul <?php echo attributes($attributes); ?>>
	<?php foreach ($items as $_item): ?>
		<?php if (url_is_active($_item['path'])) $_item['attributes']['class'][] = 'active'; ?>
		<?php
			if (!empty($_item['sub'])) {
				$_item['attributes']['class'][] = 'dropdown';
				$_item['sub']['attributes']['class'][] = 'dropdown-menu';
				$_item['attributes']['class'][] = 'dropdown-toggle';
				$_item['attributes']['data-toggle'] = 'dropdown';
				$_item['title'] .= '<b class="caret"></b>';
			}
		?>
		<li<?php echo attributes(array('class' => @$_item['attributes']['class'])); ?>>
			<?php if (!empty($_item['badge'])) $_item['title'] .= '&nbsp;' . theme('components/badge', $_item['badge']); ?>
			<?php echo l($_item); ?>
			<?php if (!empty($_item['sub'])) echo theme('components/menu', array('is_sub' => TRUE) + $_item['sub']); ?>
		</li>
	<?php endforeach; ?>
</ul>

