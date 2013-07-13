<?php

defined('MAX_ROWS_DISPLAY') or define('MAX_ROWS_DISPLAY', 50);

load_model('user');
load_model('form');
load_model('scaffolding');
load_model('schema');
load_model('menu');
load_model('theme');

global $content_table;
global $content_table_title;
global $schema;

if(!scaffolding_check_action($content_table, 'list'))
	user_login_or_403();

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

$primary = (array) @$schema[$content_table]['primary key'];

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
	foreach ($schema[$content_table]['fields'] as $field_name => $field_info) {
		if(!@$field_info['hidden'])
			$header[] = t(val($field_info['display name'], check_plain($field_name)));
	}
}

$content_q = scaffolding_list($content_table, $start, $limit);
while($content_row = ($content_q->fetchAssoc())) {
	$row = array();

	if($content_table === 'files') {
		load_model('file');
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
		foreach($content_row as $field => $value)
			if(!@$schema[$content_table]['fields'][$field]['hidden'])
				$row[$field] = check_plain($value);
	}

	if($primary && ($can_edit || $can_remove)) {
		$id = implode(',', array_intersect_key($content_row, array_flip($primary)));
		$actions = array(
			'view' => 'array',
			'items' => array());

		if($can_edit)
			$actions['items'][] = array(
				'title' => '<i class="icon-pencil"></i>',
				'path' => 'scaffolding/'.$content_table.'/edit/'.$id,
				'attributes' => array(
					'title' => t('Edit'),
				),
				'view' => 'link',
			);

		if($can_remove)
			$actions['items'][] = array(
				'title' => '<i class="icon-remove"></i>',
				'path' => 'scaffolding/'.$content_table.'/remove/'.$id,
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
	'table_description' => t(@$schema[$content_table]['description']),
	'table_content_table' => array(
		'header' => $header,
		'rows' => $rows,
	),
	'table_menu' => scaffolding_get_table_menu($content_table),
	'tables_menu' => scaffolding_get_tables_menu(),
	'display_form' => ($start != 0 || count($rows) >= MAX_ROWS_DISPLAY) ? $display_form : NULL,
));
