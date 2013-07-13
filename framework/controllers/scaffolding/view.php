<?php

load_model('scaffolding');
load_model('schema');
load_model('theme');

global $content_table;
global $content_id;
global $schema;

# Not viewable by current user
if(!scaffolding_check_action($content_table, 'view'))
	user_login_or_403();

# Get the item
$item = scaffolding_get_item($content_table, $content_id);

# Item not found
if(empty($item))
	show_404();

# Show the item
return theme($schema[$content_table]['view'], array(
	'item' => $item,
	'id' => $content_id,
	'table' => $content_table,
	'schema' => $schema[$content_table],
));
