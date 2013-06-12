<?php

require_once 'database.php';
require_once 'theme.php';
require_once 'form.php';
require_once 'user.php';

function scaffolding_tables_menu() {
	$tables_menu = array(
		'attributes' => array(
			'class' => array(
				'nav',
				'nav-tabs',
				'nav-stacked',
			),
		),
		'items' => array(),
	);
	try {
		$tables_q = db_query('SHOW TABLES');
		$allowed_tables = (array) config('scaffolding.tables');
		while($_table_info = ($tables_q->fetchAssoc())) {
			$_table_name = array_pop($_table_info);
			if(isset($allowed_tables[$_table_name]))
				$tables_menu['items'][] = array(
					'title' => val($allowed_tables[$_table_name], $_table_name),
					'path' => 'scaffolding/'.$_table_name,
				);
		}
	} catch (Exception $e) {
		show_403();
	}
	return $tables_menu;
}

function scaffolding_menu($table_name) {

	$menu['items'][] = array(
		'title' => t('Table content'),
		'path' => 'scaffolding/'.$table_name.'/index',
	);

	if(config('scaffolding.info'))
	$menu['items'][] = array(
		'title' => t('Table info'),
		'path' => 'scaffolding/'.$table_name.'/info',
	);

	$menu['items'][] = array(
		'title' => t('Add content'),
		'path' => 'scaffolding/'.$table_name.'/add',
	);

	return $menu;
}


if(!config('scaffolding.enabled'))
	show_404();

$access = config('scaffolding.access', 'MODERATOR_RID');
defined($access) or show_404();
user_role_required(constant($access));

$table_name = url_segment(1);
if($table_name) {
	$allowed_tables = (array) config('scaffolding.tables');
	isset($allowed_tables[$table_name]) or show_404();
}

switch(url_segment(2)) {
case 'add':
case 'edit':
case 'remove':
	return load_controller('scaffolding/edit');
default:
	return load_controller('scaffolding/index');
}
