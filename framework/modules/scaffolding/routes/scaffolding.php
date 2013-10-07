<?php

$routes['scaffolding_index'] = array(
	'url' => 'scaffolding',
	'controller' => 'scaffolding',
);

$routes['scaffolding_table'] = array(
	'url' => 'scaffolding/:table',
	'controller' => 'scaffolding',
	'args' => array(':table'),
);

$routes['scaffolding_item_add'] = array(
	'url' => 'scaffolding/:table/add',
	'controller' => 'scaffolding',
	'args' => array(':table', 'add'),
);

$routes['scaffolding_item_edit'] = array(
	'url' => 'scaffolding/:table/:itemid/edit',
	'controller' => 'scaffolding',
	'args' => array(':table', 'edit', ':itemid'),
);

$routes['scaffolding_item_remove'] = array(
	'url' => 'scaffolding/:table/:itemid/remove',
	'controller' => 'scaffolding',
	'args' => array(':table', 'remove', ':itemid'),
);
