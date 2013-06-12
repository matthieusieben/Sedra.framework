<?php global $_modal; ?>
<?php if(!isset($_modal)) $_modal = 0; ?>
<?php $attributes = (array) @$attributes; ?>
<?php if(!@$id) $id = !empty($attributes['id']) ? $attributes['id'] : '_sedra_modal_'.$_modal++; ?>
<?php $attributes['id'] = $id; ?>
<?php $attributes += array(
	'role' => 'dialog',
	'aria-hidden' => 'true',
	'aria-labelledby' => '_'.$id.'_label',
); ?>
<?php $attributes['class'][] = 'modal'; ?>
<?php $attributes['class'][] = 'hide'; ?>
<?php $attributes['class'][] = 'fade'; ?>
<div<?php echo attributes($attributes); ?>>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<?php if (@$title): ?>
			<h3 id="<?php echo $attributes['aria-labelledby']; ?>">
				<?php echo $title ?>
			</h3>
		<?php endif ?>
	</div>
	<?php if (@$message): ?>
		<div class="modal-body">
			<p><?php echo $message ?></p>
		</div>
	<?php endif ?>
	<div class="modal-footer">
		<?php $close = (array) @$close; ?>
		<?php $close += array('title'=>t('Close'),'uri'=>'','anchor'=>'','attributes'=>array()); ?>
		<?php $close['attributes'] += array('data-dismiss'=>'modal', 'aria-hidden'=>'true'); ?>
		<?php $close['attributes']['class'][] = 'btn'; ?>
		<?php echo l($close); ?>

		<?php $confirm = (array) @$confirm; ?>
		<?php $confirm += array('title'=>t('Confirm'),'attributes'=>array()); ?>
		<?php $confirm['attributes']['class'][] = 'btn'; ?>
		<?php $confirm['attributes']['class'][] = 'btn-primary'; ?>
		<?php if(strip_tags($confirm['title']) === '') $confirm['title'] = t('Confirm'); ?>
		<?php echo l($confirm); ?>
	</div>
</div>