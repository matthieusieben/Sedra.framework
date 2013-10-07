<?php

defined('MAX_ROWS_DISPLAY') or define('MAX_ROWS_DISPLAY', 20);

require_once 'includes/form.php';
require_once 'includes/schema.php';
require_once 'includes/menu.php';
require_once 'includes/pagination.php';
require_once 'includes/theme.php';
require_once 'includes/scaffolding.php';

global $content_table;
global $content_table_info;
global $content_table_title;

if(!scaffolding_check_action($content_table, 'list'))
	show_401();

if(empty($content_table)) {
	$tables_menu = scaffolding_get_tables_menu();

	if(empty($tables_menu['items']))
		show_404();

	return theme('scaffolding/list', array(
		'title' => t('Site content'),
		'tables_menu' => $tables_menu,
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
			'default' => val($_GET['start'], 0),
		),
		'limit' => array(
			'label' => t('Limit'),
			'type' => 'number',
			'min' => 1,
			'default' => val($_GET['limit'], MAX_ROWS_DISPLAY),
		),
		'update' => array(
			'type' => 'submit',
			'label' => t('Update'),
		),
	),
);
if(form_run($display_form)) {
	$display_form_values = form_values($display_form);
	$start = $display_form_values['start'];
	$limit = $display_form_values['limit'];
} else {
	$start = @$_GET['start'];
	$limit = @$_GET['limit'];
}

$primary = (array) @$content_table_info['primary key'];

$header = array();
$rows = array();

$can_edit = scaffolding_check_action($content_table, 'edit');
$can_remove = scaffolding_check_action($content_table, 'remove');

if($primary && ($can_edit || $can_remove)) {
	$header[] = '';
}

if($content_table === 'files') {
	$header[] = t('File');
	$header[] = t('File url');
} else {
	foreach ($content_table_info['fields'] as $field_name => $field_info) {
		if(!@$field_info['hidden'])
			$header[] = t(val($field_info['display name'], check_plain($field_name)));
	}
}

$content_q = scaffolding_list($content_table, $start, $limit);
while($content_row = ($content_q->fetchAssoc())) {
	$row = array();

	if($content_table === 'files') {
		require_once 'includes/file.php';
		$file_info = file_info(array('fid' => $content_row['fid']));
		$row = array(
			theme_file($file_info),
			array(
				'view' => 'link',
				'title' => url($file_info['url']),
				'path' => $file_info['url']['path'],
			),
		);
	}
	else {
		foreach($content_row as $field => $value) {
			if(!@$content_table_info['fields'][$field]['hidden']) {
				if(isset($content_table_info['fields'][$field]['options'][$value])) {
					$row[$field] = $content_table_info['fields'][$field]['options'][$value];
				}
				else {
					$row[$field] = check_plain($value);
				}
			}
		}
	}

	if($primary && ($can_edit || $can_remove)) {
		$id = implode(',', array_intersect_key($content_row, array_flip($primary)));
		$actions = array(
			'view' => 'array',
			'items' => array());

		if($can_edit)
			$actions['items'][] = array(
				'title' => '<i class="icon-pencil"></i>',
				'path' => router_create_path('scaffolding_item_edit', array('table' => $content_table, 'itemid' => $id)),
				'attributes' => array(
					'title' => t('Edit'),
				),
				'view' => 'link',
			);

		if($can_remove)
			$actions['items'][] = array(
				'title' => '<i class="icon-remove"></i>',
				'path' => router_create_path('scaffolding_item_remove', array('table' => $content_table, 'itemid' => $id)),
				'attributes' => array(
					'title' => t('Remove'),
				),
				'view' => 'components/link_modal',
			);

		array_unshift($row, $actions);
	}

	$rows[] = $row;
}

return theme('scaffolding/list', array(
	'title' => $content_table_title,
	'table_name' => $content_table,
	'table_description' => t(@$content_table_info['description']),
	'table_content_table' => array(
		'header' => $header,
		'rows' => $rows,
	),
	'table_menu' => scaffolding_get_table_menu($content_table),
	'tables_menu' => scaffolding_get_tables_menu(),
	'display_form' => ($start != 0 || count($rows) >= MAX_ROWS_DISPLAY) ? $display_form : NULL,
	'pagination' => pagination(
		scaffolding_list_query($content_table)->countQuery()->execute()->fetchField(),
		$start,
		'scaffolding/'.$content_table.'/index?start=!start&limit=!limit',
		$limit
	),
));
