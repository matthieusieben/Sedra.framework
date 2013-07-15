<?php

$create_setup = FALSE;

try {
	require_once 'framework/bootstrap.php';
} catch (FrameworkException $e) {
	$create_setup = TRUE;
}

require_once 'includes/theme.php';
require_once 'includes/schema.php';

if($create_setup) {
	# TODO
	fatal('Settings file creation not implemented. Please create `settings.php` in the application directory.');
} else {

	$first_install = !db_table_exists('users') || !db_table_exists('sessions');

	if(!$first_install) {
		require_once 'includes/user.php';
		user_role_required(ADMINISTRATOR_RID);
	}

	foreach (schema_get_all() as $name => $table) {
		if (!empty($table) && !db_table_exists($name)) {
			# XXX
			echo "installing {$name}. <br/>\n";

			sedra_schema_init($name, $table);
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
	}
}
