<?php global $home_url, $user; ?>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<?php
				echo l(array(
					'title' => ($site_logo ? '<img src="'. $site_logo .'" alt="'. $site_name .'" /> ' : '') . $site_name,
					'path' => 'index',
					'attributes' => array(
						'title' => $site_name,
						'class' => array('brand'),
					),
				));
			?>
			<div class="nav-collapse collapse">
				<?php echo theme('components/menu', $menus['main']); ?>
				<?php $menus['secondary']['attributes']['class'][] = 'pull-right'; ?>
				<?php echo theme('components/menu', $menus['secondary']); ?>
			</div>
		</div>
	</div>
</div>
