<?php

require_once 'theme.php';
require_once 'form.php';
require_once 'user.php';

$table_name = url_segment(1);
$action = url_segment(2);
$id = url_segment(3);

$primary = NULL;
$table_info = array();

try {
	$table_info_q = db_query("DESCRIBE {{$table_name}}");
	while($table_field_info = ($table_info_q->fetchAssoc())) {
		if($table_field_info['Key'] === 'PRI')
			$primary = $table_field_info['Field'];
		$table_info[] = $table_field_info;
	}
} catch(Exception $e) {
	show_404();
}

if(empty($table_info))
	throw new FrameworkException(t('This table has no field.'), 404);

if(!$primary)
	throw new FrameworkException(t('This table has no primary key.'), 404);

$values = array();
switch ($action) {
case 'remove':
	if(!$id) show_404();
	db_delete($table_name)->condition($primary, $id)->execute();
	return redirect('scaffolding/'.$table_name);

case 'edit':
	if(!$id) show_404();
	$q = db_select($table_name, 't')->fields('t')->condition($primary, $id)->execute();
	$values = $q->fetchAssoc();
	if(!$values)
		throw new FrameworkException(t('This item does not exists.'), 404);
	break;

case 'add':
	# Nothing to do ...
	break;

default:
	show_404();
	break;
}

foreach($table_info as $table_field_info) {
	$field = $table_field_info['Field'];
	$field_type = $table_field_info['Type'];
	$type = NULL;

	if(($p = strpos($field_type, '(')) !== FALSE)
		$field_type = substr($field_type, 0, $p);
	if(($p = strpos($field_type, ' ')) !== FALSE)
		$field_type = substr($field_type, 0, $p);

	switch($field_type) {
	case 'text':
		$type = 'textarea';
		break;
	case 'varchar':
		$type = 'text';
		break;
	case 'tinyint':
	case 'int':
		$type = 'number';
		break;
	}

	if(!isset($type))
		continue;

	$form['fields'][$field] = array(
		'label' => check_plain($field),
		'type' => $type,
		'default' => val($values[$field], NULL),
		'attributes' => array(
			'class' => array(
				'input-xxlarge'
			),
		),
	);
}

$form['fields'][] = array(
	'type' => 'actions',
	'fields' => array(
		array(
			'type' => 'submit',
			'label' => $id ? t('Edit') : t('Add'),
		),
		array(
			'view' => 'html',
			'html' => l(array(
				'title' => t('Cancel'),
				'path' => 'scaffolding/'.$table_name,
				'attributes' => array('class' => array(
					'btn',
					'btn-danger',
				)),
			)),
		),
	),
);

if(form_run($form) && form_is_valid($form)) {
	$form_values = form_values($form);
	try {
		if($id) {
			db_update($table_name)
				->fields($form_values)
				->condition($primary, $id)
				->execute();
		}
		else {
			$id = db_insert($table_name)
				->fields($form_values)
				->execute();
		}
		redirect('scaffolding/'.$table_name);
	} catch (Exception $e) {
		$form['error'] = $e->getMessage();
		$form['valid'] = FALSE;
	}
}



return theme('scaffolding/edit', array(
	'title' => $id ?
		t('@table_name : edit', array('@table_name' => $table_name)) :
		t('@table_name : add', array('@table_name' => $table_name)) ,
	'tables_menu' => scaffolding_tables_menu(),
	'scaffolding_menu' => scaffolding_menu($table_name),
	'form' => $form,
));