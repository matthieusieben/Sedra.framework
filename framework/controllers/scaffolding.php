<?php

define('MAX_ROWS_DISPLAY', 50);

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
		while($_table_info = ($tables_q->fetchAssoc())) {
			$_table_name = array_pop($_table_info);
			if(in_array($_table_name, (array) config('scaffolding.tables')))
				$tables_menu['items'][] = array(
					'title' => $_table_name,
					'path' => 'scaffolding/'.$_table_name,
				);
		}
	} catch (Exception $e) {
		show_403();
	}
	return $tables_menu;
}

function scaffolding_menu($table_name) {
	return array(
		'items' => array(
			array(
				'title' => t('Table content'),
				'path' => 'scaffolding/'.$table_name.'/index',
			),
			array(
				'title' => t('Table info'),
				'path' => 'scaffolding/'.$table_name.'/info',
			),
			array(
				'title' => t('Add content'),
				'path' => 'scaffolding/'.$table_name.'/add',
			),
		),
	);
}

require_once 'theme.php';
require_once 'form.php';
require_once 'user.php';

if(!config('scaffolding.enabled'))
	show_404();

user_role_required(MODERATOR_RID);

$table_name = url_segment(1);
$action = url_segment(2);

if($table_name && !in_array($table_name, (array) config('scaffolding.tables')))
	show_404();

switch($action) {
case 'add':
case 'edit':
case 'remove':
	return load_controller('scaffolding/edit');
}

$tables_menu = scaffolding_tables_menu();
$scaffolding_menu = scaffolding_menu($table_name);

if(!$table_name) {
	return theme('scaffolding/index', array(
		'title' => t('Scaffolding'),
		'table_name' => $table_name,
		'tables_menu' => $tables_menu,
	));
}

$table_info = array();
$primary = NULL;
try {
	$table_info_q = db_query("DESCRIBE {{$table_name}}");
	while($table_field_info = ($table_info_q->fetchAssoc())) {
		if($table_field_info['Key'] === 'PRI')
			$primary = $table_field_info['Field'];
		$table_info[] = $table_field_info;
	}
} catch(Exception $e) {
	throw new FrameworkException(t('Table @table_name doesn\'t exist.', array('@table_name' => $table_name)), 404);
}

if(empty($table_info)) {
	throw new FrameworkException(t('This table has no field.'), 404);
}

if($action === 'info') {
	return theme('scaffolding/table_info', array(
		'title' => t('@table_name : info', array('@table_name' => $table_name)),
		'table_name' => $table_name,
		'tables_menu' => $tables_menu,
		'scaffolding_menu' => $scaffolding_menu,
		'table_info_table' => array(
			'header' => array_keys($table_info[0]),
			'rows' => $table_info,
		),
	));
}

$display_form = array(
	'method' => 'get',
	'style' => 'inline',
	'fields' => array(
		'start' => array(
			'label' => t('Start'),
			'type' => 'number',
			'min' => 0,
			'default' => 0,
		),
		'limit' => array(
			'label' => t('Limit'),
			'type' => 'number',
			'min' => 1,
			'default' => MAX_ROWS_DISPLAY,
		),
		'update' => array(
			'type' => 'submit',
			'label' => t('Update'),
		),
	),
);
form_run($display_form);
$display_form_values = form_values($display_form);
$start = $display_form_values['start'];
$limit = $display_form_values['limit'];

$header = array();
$rows = array();

if($primary) {
	$header[] = '';
}

foreach ($table_info as $field_info) {
	$header[] = $field_info['Field'];
}

$content_q = db_select($table_name, 't')->fields('t')->range($start, $limit)->execute();
while($content_row = ($content_q->fetchAssoc())) {

	foreach($content_row as $field => $value) {
		if(count($table_info) > 5 && strlen($value) > 20)
			$value = check_plain(substr($value, 0, 16)) . '&hellip;';
		else
			$value = check_plain($value);

		$content_row[$field] = $value;
	}

	if($primary) {
		array_unshift($content_row,
			l(array(
				'title' => '<i class="icon-edit"></i>',
				'path' => 'scaffolding/'.$table_name.'/edit/'.$content_row[$primary],
				'attributes' => array(
					'title' => t('Edit'),
				),
			))
			.'&nbsp;'.
			l(array(
				'title' => '<i class="icon-remove"></i>',
				'path' => 'scaffolding/'.$table_name.'/remove/'.$content_row[$primary],
				'attributes' => array(
					'title' => t('Remove'),
				),
			)));
	}

	$rows[] = $content_row;
}

return theme('scaffolding/table_content', array(
	'title' => t('@table_name : content', array('@table_name' => $table_name)),
	'table_name' => $table_name,
	'tables_menu' => $tables_menu,
	'scaffolding_menu' => $scaffolding_menu,
	'display_form' => ($start != 0 || count($rows) > MAX_ROWS_DISPLAY) ? $display_form : NULL,
	'table_content_table' => array(
		'header' => $header,
		'rows' => $rows,
	),
));
