<?php

$create_setup = FALSE;

try {
	require_once 'framework/bootstrap.php';
} catch (Exception $e) {
	$create_setup = TRUE;
}

load_model('schema');
load_model('theme');
load_model('kprintr');

if($create_setup) {
	# TODO
	fatal('Settings file creation not implemented. Please create `settings.php` in the application directory.');
} else {
	$first_install = !db_table_exists('users');

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

		load_model('user');
		user_register(array(
			'uid' => 1,
			'rid' => 1,
			'name' => $name,
			'mail' => $mail,
			'pass' => $pass,
			'language' => config('site.language'),
			'timezone' => config('date.timezone'),
			'created' => REQUEST_TIME,
			'access' => REQUEST_TIME,
			'login' => REQUEST_TIME,
			'status' => 1,
		));
		user_login($mail, $pass);

		load_model('message');
		message(MESSAGE_SUCCESS, t('The database was successfully created.'));

		redirect('index');
	}
}
