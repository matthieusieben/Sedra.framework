<?php

load_model('user');
load_model('form');
load_model('scaffolding');
load_model('schema');
load_model('menu');
load_model('theme');

global $content_id;
global $content_table;
global $content_action;
global $schema;

if(!scaffolding_check_action($content_table, $content_action))
	user_login_or_403();

if(empty($schema[$content_table]))
	throw new FrameworkException(t('This table is not defined.'), 404);

switch ($content_action) {
case 'remove':
case 'edit':
	if(empty($schema[$content_table]['primary key']))
		throw new FrameworkException(t('This table has no primary key.'), 404);
case 'add':
	break;
default:
	show_404();
}

$values = NULL;
switch ($content_action) {
case 'remove':
	if(!$content_id) show_404();
	scaffolding_delete_id($content_table, $content_id);
	return redirect("scaffolding/{$content_table}/index");

case 'edit':
	if(!$content_id) show_404();
	$values = scaffolding_get_item($content_table, $content_id);
	if(!$values)
		throw new FrameworkException(t('This item does not exists.'), 404);
	break;
}

$form = scaffolding_get_edit_form($content_table, $content_action, $content_id, $values);

scaffolding_handle_form($content_table, $content_action, $content_id, $form);

breadcrumb_add(array(
	'path' => 'scaffolding/'.$content_table.'/'.($content_id ? 'edit/'.$content_id : 'add'),
	'title' => $content_id
		? t('Edit content')
		: t('Add content'),
));

return theme('scaffolding/edit', array(
	'title' => $content_id
		? t('!table_name : edit', array('!table_name' => t(val($schema[$content_table]['display name'], $content_table))))
		: t('!table_name : add', array('!table_name' => t(val($schema[$content_table]['display name'], $content_table)))),
	'table_name' => $content_table,
	'table_description' => t(@$schema[$content_table]['description']),
	'table_menu' => scaffolding_get_table_menu($content_table),
	'tables_menu' => scaffolding_get_tables_menu(),
	'form' => $form,
));
