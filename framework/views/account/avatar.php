<?php if(@$mini): ?>
	<span class="avatar-image">
		<img src="<?php echo $avatar_url ?>" alt="<?php echo $account->name ?>" width="<?php echo $size ?>" />
	</span>
<?php else: ?>
	<div class="avatar">
		<span class="avatar-image">
			<img src="<?php echo $avatar_url ?>" alt="<?php echo $account->name ?>" width="<?php echo $size ?>" />
		</span>
		<?php if(!empty($caption)): ?>
			<span class="avatar-caption">
				<?php if(is_string($caption)): ?>
					<?php echo $caption; ?>
				<?php else: ?>
					<?php echo t('This is your !gravatar.', array('!gravatar' => l(array('title' => t('Gravatar'), 'uri' => 'http://gravatar.com', 'target' => 'blank')))) ?>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	</div>
<?php endif; ?>
