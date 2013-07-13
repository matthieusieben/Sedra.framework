<?php global $user ?>
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
			<b class="name"><?php echo $account->name ?></b>
			<?php if ($user->uid === $account->uid): ?>
				<p class="email"><?php echo $account->mail ?></p>
			<?php endif ?>
		</div>
	</div>
<?php endif ?>