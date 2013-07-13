<?php

$create_setup = FALSE;

try {
	require_once 'framework/bootstrap.php';
} catch (FrameworkException $e) {
	$create_setup = TRUE;
}

load_model('theme');
load_model('schema');
load_model('kprintr');

if($create_setup) {
	# TODO
	fatal('Settings file creation not implemented. Please create `settings.php` in the application directory.');
} else {

	$first_install = !db_table_exists('users') || !db_table_exists('sessions');

	if(!$first_install) {
		load_model('user');
		user_role_required(ADMINISTRATOR_RID);
	}

	global $schema;
	foreach ($schema as $name => $table) {
		if (!empty($table) && !db_table_exists($name)) {
			# XXX
			echo "installing {$name}. \n";

			sedra_schema_init($name, $table);
			sedra_schema_install($table);
		}
	}

	if($first_install) {
		# TODO from form
		$name = 'Administrator';
		$mail = 'admin@example.com';
		$pass = 'admin';

		load_model('scaffolding');
		scaffolding_add_item('users', array(
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
		));
	}
}
