<?php

$routes['scaffolding_index'] = array(
	'url' => 'scaffolding',
	'controller' => 'scaffolding',
);

$routes['scaffolding_table'] = array(
	'url' => 'scaffolding/(?<table>\w+)',
	'controller' => 'scaffolding',
	'args' => array('$table'),
);

$routes['scaffolding_item_add'] = array(
	'url' => 'scaffolding/(?<table>\w+)/add',
	'controller' => 'scaffolding',
	'args' => array('$table', 'add'),
);

$routes['scaffolding_item_edit'] = array(
	'url' => 'scaffolding/(?<table>\w+)/edit/(?<itemid>\w+)',
	'controller' => 'scaffolding',
	'args' => array('$table', 'edit', '$itemid'),
);

$routes['scaffolding_item_remove'] = array(
	'url' => 'scaffolding/(?<table>\w+)/remove/(?<itemid>\w+)',
	'controller' => 'scaffolding',
	'args' => array('$table', 'remove', '$itemid'),
);
