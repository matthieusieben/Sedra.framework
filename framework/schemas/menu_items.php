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
		'parent' => array(
			'type' => 'int',
			'not null' => FALSE,
			'unsigned' => TRUE,
			'display name' => t('Parent item'),
		),
		'title' => array(
			'type' => 'varchar',
			'not null' => TRUE,
			'length' => 128,
			'display name' => t('Item title'),
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
		'path' => array(
			'type' => 'varchar',
			'not null' => FALSE,
			'length' => 512,
			'display name' => t('Path'),
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
		'view' => MODERATOR_RID,
		'list' => MODERATOR_RID,
		'add' => MODERATOR_RID,
		'edit' => MODERATOR_RID,
		'remove' => MODERATOR_RID,
	),
	'order' => array(
		'menu' => 'ASC',
		'parent' => 'ASC',
		'weight' => 'ASC',
	),
);
