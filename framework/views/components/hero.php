<div class="hero-unit">
	<?php if (@$title): ?>
		<header>
			<h1><?php echo $title ?></h1>
		</header>
	<?php endif ?>
	<?php if (isset($html)): ?>
		<div class="content">
			<?php if (is_string($html)): ?>
				<?php echo $html ?>
			<?php elseif(is_array($html)): ?>
				<?php echo theme($html) ?>
			<?php endif ?>
		</div>
	<?php endif ?>
</div>