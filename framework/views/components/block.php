<div class="block">
	<?php if (!empty($title)): ?>
		<header>
			<h2 class="block-title">
				<?php echo $title ?>
			</h2>
		</header>
		<?php unset($__data['title']); ?>
	<?php endif ?>

	<div class="content">
		<?php echo theme('html', $__data) ?>
	</div>
</div>