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
	show_401();

if($content_action === 'edit' || $content_action === 'remove')
	if(empty($content_table_info['primary key']))
		throw new FrameworkException(t('This table has no primary key.'), 404);

$form = NULL;
$values = NULL;
switch ($content_action) {
default:
	return show_404();

case 'remove':
	if(!$content_id) show_404();
	scaffolding_handle_form($content_table, $content_action, $content_id, $form);
	return redirect(router_create_path('scaffolding_table', array('table' => $content_table)));

case 'edit':
	if(!$content_id) show_404();
	$values = scaffolding_get_item($content_table, $content_id);
	if(!$values)
		throw new FrameworkException(t('This item does not exists.'), 404);
	# no break

case 'add':
	$form = scaffolding_get_edit_form($content_table, $content_action, $content_id, $values);
	scaffolding_handle_form($content_table, $content_action, $content_id, $form);
	break;
}

breadcrumb_add(array(
	'path' => router_create_path($content_id ? 'scaffolding_item_edit' : 'scaffolding_item_add', array('table' => $content_table, 'itemid' => $content_id)),
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
