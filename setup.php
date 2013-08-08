<?php

$create_setup = FALSE;

try {
	require_once 'framework/bootstrap.php';
} catch (FrameworkException $e) {
	$create_setup = TRUE;
}

cache_delete();

require_once 'includes/theme.php';
require_once 'includes/schema.php';
require_once 'includes/menu.php';

if($create_setup) {
	# TODO
	fatal('Settings file creation not implemented. Please create `settings.php` in the application directory.');
} else {

	$first_install = !db_table_exists('users') || !db_table_exists('sessions');

	if(!$first_install) {
		require_once 'core/user.php';
		user_role_required(ADMINISTRATOR_RID);
	}

	foreach (schema_get_all() as $name => $table) {
		if (!empty($table) && !db_table_exists($name)) {
			# XXX
			echo "installing {$name}. <br/>\n";

			sedra_schema_install($table);
		}
	}

	if($first_install) {
		# TODO from form
		$name = 'Administrator';
		$mail = 'admin@example.com';
		$pass = 'admin';

		db_insert('users')->fields(array(
			'rid' => ADMINISTRATOR_RID,
			'name' => $name,
			'mail' => $mail,
			'pass' => password_hash($pass),
			'language' => config('site.language'),
			'timezone' => config('date.timezone'),
			'created' => REQUEST_TIME,
			'access' => REQUEST_TIME,
			'login' => REQUEST_TIME,
			'status' => 1,
		))->execute();

		#TODO test this.
		menu_add(array(
			'menu' => 'main',
			'path' => 'index',
			'title' => 'Home',
			'weight' => 0,
		));
		menu_add(array(
			'menu' => 'main',
			'path' => 'contact',
			'title' => 'Contact',
			'weight' => 50,
		));
		menu_add(array(
			'menu' => 'secondary',
			'path' => 'account',
			'title' => 'Account',
			'role' => NULL,
			'weight' => 50,
		));
		menu_add(array(
			'menu' => 'secondary',
			'path' => 'account/login',
			'title' => 'Login',
			'role' => 0,
			'parent' => 'account/login',
		));
		menu_add(array(
			'menu' => 'secondary',
			'path' => 'account/index',
			'title' => 'Account',
			'role' => 1,
			'parent' => 'account',
		));
		menu_add(array(
			'menu' => 'secondary',
			'path' => 'account/logout',
			'title' => 'Logout',
			'role' => 1,
			'parent' => 'account',
			'weight' => 50,
		));
		menu_add(array(
			'menu' => 'user',
			'path' => 'account/index',
			'title' => 'Account details',
			'role' => 1,
			'weight' => 0,
		));
		menu_add(array(
			'menu' => 'user',
			'path' => 'account/credentials',
			'title' => 'Edit your credentials',
			'role' => 1,
			'weight' => 1,
		));
		global $config;
		if(!empty($config['modules']['scaffolding'])) {
			menu_add(array(
				'menu' => 'secondary',
				'path' => 'scaffolding',
				'title' => 'Site content',
				'role' => NULL,
				'weight' => 45,
			));
		}
	}
}
