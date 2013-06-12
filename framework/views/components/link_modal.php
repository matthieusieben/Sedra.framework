<?php global $_link_modal; ?>
<?php if(!isset($_link_modal)) $_link_modal = 0; ?>
<?php if(!@$id) $id = !empty($attributes['id']) ? $attributes['id'] : '_sedra_link_modal_'.($_link_modal++); ?>
<?php $_original_link = $__data; ?>
<?php echo theme('components/modal', array(
	'id' => $id,
	'title' => val($header, t('Are you sure?')),
	'message' => val($message, t('This cannot be undone.')),
	'confirm' => $_original_link, # Original link.
)); ?>
<?php $_original_link['anchor'] = $id; ?>
<?php $_original_link['attributes']['data-toggle'] = 'modal'; ?>
<?php echo l($_original_link); ?>