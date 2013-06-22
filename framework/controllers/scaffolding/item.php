<?php

load_model('scaffolding');
load_model('schema');
load_model('theme');
load_model('user');

global $content_table;
global $content_id;
global $schema;

if(!scaffolding_check_action($content_table, 'view'))
	show_403();

$item = scaffolding_get_item($content_table, $content_id);

if(empty($item))
	if(config('devel'))
		throw new FrameworkException(t('The requested item does not exist.'), 404);
	else
		show_404();

$item += array(
	'view' => @$schema[$content_table]['view'],
);

if(empty($item['view']))
	if(config('devel'))
		throw new FrameworkException(t('There is no view defined for this item.'), 404);
	else
		show_404();

return theme($item);
