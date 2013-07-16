<?php

require_once 'includes/form.php';
require_once 'includes/schema.php';
require_once 'includes/menu.php';
require_once 'includes/theme.php';
require_once 'includes/scaffolding.php';

global $content_id;
global $content_table;
global $content_table_info;
global $content_action;
global $content_table_title;

if(!scaffolding_check_action($content_table, $content_action))
	show_403();

switch ($content_action) {
case 'remove':
case 'edit':
	if(empty($content_table_info['primary key']))
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
	'title' => $content_table_title,
	'table_name' => $content_table,
	'table_description' => t(@$content_table_info['description']),
	'table_menu' => scaffolding_get_table_menu($content_table),
	'tables_menu' => scaffolding_get_tables_menu(),
	'form' => $form,
));
