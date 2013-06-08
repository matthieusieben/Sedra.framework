<?php if(@$mini): ?>
	<div class="avatar avatar-mini clearfix">
		<div class="avatar-image">
			<img src="<?php echo $avatar_url ?>" alt="<?php echo $account->name ?>" width="<?php echo $size ?>" />
		</div>
	</div>
<?php else: ?>
	<div class="avatar clearfix">
		<div class="avatar-image">
			<img src="<?php echo $avatar_url ?>" alt="<?php echo $account->name ?>" width="<?php echo $size ?>" />
		</div>
		<div class="avatar-caption">
			<b><?php echo $account->name ?></b>
			<p class="email"><?php echo $account->mail ?></p>
		</div>
	</div>
<?php endif; ?>
