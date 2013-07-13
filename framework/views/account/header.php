<div id="user-header" class="row">
	<div class="span3">
		<?php echo theme_avatar($account, 40); ?>
	</div>
	<div class="span8">
		<?php $menus['user']['attributes']['class'][] = 'nav-pills'; ?>
		<?php echo theme('components/menu', $menus['user']); ?>
	</div>
</div>