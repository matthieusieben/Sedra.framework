<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>

<form id="login-form" action="<?php echo $action; ?>" method="post">
	<fieldset>
		<legend><?php p('Log in'); ?></legend>
		<div>
			<label for="login"><?php p('Login'); ?></label>
			<input type="text" id="login" name="user-name">
		</div>
		<div>
			<label for="password"><?php p('Password'); ?></label>
			<input type="password" id="password" name="user-pass">
		</div>
		<div>
			<label for="remember_me"><?php p('Remember me?'); ?></label>
			<input type="checkbox" id="remember_me" name="user-remember_me">
		</div>
		<div>
			<input type="submit" class="button" name="user-login" value="<?php p('Log in'); ?>">
		</div>
	</fieldset>
</form>

<?php require 'views/foot.php' ?>