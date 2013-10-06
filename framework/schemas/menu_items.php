<?php

return array(
	'display name' => t('Menu items'),
	'fields' => array(
		'mid' => array(
			'type' => 'serial',
			'not null' => TRUE,
			'unsigned' => TRUE,
			'hidden' => TRUE,
		),
		'menu' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('Menu'),
			'options' => array(
				'main' => t('Main menu'),
				'secondary' => t('Secondary menu'),
				'user' => t('User menu'),
			),
		),
		'title' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('Item title'),
		),
		'language' => array(
			'type' => 'varchar',
			'not null' => FALSE,
			'length' => 10,
			'display name' => t('Language'),
			'options' => (array) config('site.languages', 'en'),
		),
		'path' => array(
			'type' => 'varchar',
			'not null' => FALSE,
			'length' => 128,
			'display name' => t('Path'),
		),
		'parent' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'display name' => t('Parent item'),
		),
		'role' => array(
			'type' => 'enum',
			'not null' => FALSE,
			'default' => -1,
			'options' => array(
				-1 => t('Anyone'),
				ANONYMOUS_RID => t('Anonymous only'),
				AUTHENTICATED_RID => t('Authenticated users'),
				MODERATOR_RID => t('Moderators'),
				ADMINISTRATOR_RID => t('Administrators'),
			),
			'display name' => t('Required role'),
			'description' => t('Leave empty if you want this item to appear only when it has visible sub pages.')
		),
		'weight' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'default' => 0,
			'display name' => t('Position'),
		),
	),
	'primary key' => array('mid'),
	'foreign keys' => array(
		'parent_item' => array(
			'table' => 'menu_items',
			'columns' => array('parent' => 'mid'),
			'cascade' => FALSE,
		),
	),
	'roles' => array(
		'view' => ADMINISTRATOR_RID,
		'list' => ADMINISTRATOR_RID,
		'add' => ADMINISTRATOR_RID,
		'edit' => ADMINISTRATOR_RID,
		'remove' => ADMINISTRATOR_RID,
	),
	'order' => array(
		'menu' => 'ASC',
		'parent' => 'ASC',
		'weight' => 'ASC',
	),
	'custom form handle' => '_schema_menu_items_form_handle',
);

function _schema_menu_items_form_handle($table, $action, $id, &$form) {
	cache_delete('menus/%');
	return scaffolding_handle_form_default($table, $action, $id, $form);
}
