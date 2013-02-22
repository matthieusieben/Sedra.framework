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
				<ul class="nav pull-right">
					<?php if(isset($user)): ?>
						<?php if(user_has_role(AUTHENTICATED_RID)): ?>
							<li><?php echo l(array('path' => 'account', 'title' => t('Account'))); ?></li>
							<li><?php echo l(array('path' => 'account/logout', 'title' => t('Logout'))); ?></li>
						<?php else: ?>
							<li><?php echo l(array('path' => 'account/login', 'title' => t('Login'))); ?></li>
						<?php endif; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
