<?php

load_model('database');
load_model('schema');

function scaffolding_get_id($table, $id) {
	global $schema;

	if(!is_array($id))
		$id = explode(',', $id);

	$pri = (array) @$schema[$table]['primary key'];

	if(empty($id) || count($id) != count($pri))
		return NULL;

	$id = array_combine($pri, $id);

	return $id;
}

function scaffolding_get_items($table, $cond) {
	global $schema;

	if(empty($schema[$table]))
		return NULL;

	$query = db_select($table, 't')->fields('t');

	if(!empty($cond))
		foreach($cond as $_col => $_value)
			$query->condition($_col, $_value);

	$result = $query->execute();

	$items = array();
	while($item = $result->fetchAssoc()) {
		$items[] = $item;
	}

	return $items;
}

function scaffolding_get_item($table, $id) {
	$cond = scaffolding_get_id($table, $id);

	if(empty($cond))
		return NULL;

	$items = scaffolding_get_items($table, $cond);
	return array_pop($items);
}

function scaffolding_add_item($table, $values) {
	global $schema;

	if(empty($schema[$table]['fields']))
		return FALSE;

	$r = db_insert($table)
		->fields(array_intersect_key($values, $schema[$table]['fields']))
		->execute();

	_scaffolding_file_field_save($table, $values);

	return $r;
}

function scaffolding_set_item($table, $id, $values) {
	global $schema;

	if(empty($schema[$table]['fields']))
		return FALSE;

	$cond = scaffolding_get_id($table, $id);
	if(empty($cond))
		return FALSE;

	$query = db_update($table)
		->fields(array_intersect_key($values, $schema[$table]['fields']));

	foreach($cond as $_col => $_value)
		$query->condition($_col, $_value);

	$r = $query->execute();

	_scaffolding_file_field_save($table, $values);

	return $r;
}

function scaffolding_delete_id($table, $id) {
	if(empty($id))
		return NULL;

	$cond = scaffolding_get_id($table, $id);
	if(empty($cond))
		return FALSE;

	$query = db_delete($table);
	foreach($cond as $_col => $_value)
		$query->condition($_col, $_value);
	return $query->execute();
}

function scaffolding_list($table, $start = NULL, $limit = NULL) {
	$query = db_select($table, 't')->fields('t');
	if(is_numeric($limit))
		$query->range((int) $start, (int) $limit);
	return $query->execute();
}

function scaffolding_check_action($table, $action) {
	load_model('user');

	global $schema;

	if($table) {
		if(isset($schema[$table]['roles'][$action]))
			if(user_has_role($schema[$table]['roles'][$action]))
				if($action === 'view') {
					if(isset($schema[$table]['view']))
						return TRUE;
				} else
					return TRUE;
		return FALSE;
	} else {
		foreach ($schema as &$table_info)
			if(isset($table_info['roles'][$action]))
				if(user_has_role($table_info['roles'][$action]))
					if($action === 'view') {
						if(isset($table_info['view']))
							return TRUE;
					} else
						return TRUE;
		return FALSE;
	}
}

function scaffolding_get_edit_form($table, $action, $id, $values = NULL) {
	global $schema;

	if(isset($schema[$table]['custom form'])) {
		$form = $schema[$table]['custom form'];
	}
	else {
		$form = array('fields' => array());
		foreach($schema[$table]['fields'] as $field_name => $field_info) {

			if(@$field_info['hidden'])
				continue;

			switch(@$field_info['type']) {
			case 'text':
			case 'blob':
				$form_field = array(
					'type' => 'textarea',
				);
				break;
			case 'varchar':
				$form_field = array(
					'type' => 'text',
					'max' => @$field_info['length'],
				);
				break;
			case 'numeric':
			case 'tinyint':
			case 'float':
			case 'int':
				$form_field = array(
					'type' => 'number',
				);
				break;
			case 'datetime':
				$form_field = array(
					'type' => 'datetime',
					'format' => 'yyyy-MM-dd hh:mm:ss',
				);
				break;
			case 'enum':
				$form_field = array(
					'type' => 'select',
					'default' => @$field_info['default'],
					'options' => @$field_info['options'],
				);
				break;
			default:
				continue 2;
			}

			$form['fields'][$field_name] = $form_field + array(
				'label' => val($field_info['display name'], $field_name),
				'required' => (bool) @$field_info['not null'],
				'wysiwyg' => @$form_field['type'] === 'textarea',
				'allowable_tags' => NULL, # No restriction
				'default' => @$field_info['default'],
				'help' => t(@$field_info['description']),
			);
		}

		foreach((array) @$schema[$table]['foreign keys'] as $fk) {
			reset($fk['columns']);
			if($fk['table'] === 'files') {
				list($field_name, $foreign_field) = each($fk['columns']);
				$form['fields'][$field_name]['type'] = 'file';
			}
			else {
				while(list($field_name, $foreign_field) = each($fk['columns'])) {

					$form['fields'][$field_name]['type'] = 'select';
					$form['fields'][$field_name]['options'] = array();

					$fis = scaffolding_list($fk['table']);
					while($fi = ($fis->fetchAssoc())) {
						$foreign_id = $fi[$foreign_field];
						$foreign_name = (!empty($fi['name'])
							? $fi['name']
							: (!empty($fi['title'])
								? $fi['title']
								: $foreign_id));

						$form['fields'][$field_name]['options'][$foreign_id] = $foreign_name;
					}
				}
			}
		}
	}

	foreach ($form['fields'] as $name => &$field) {
		if(isset($values[$name]))
			$field['value'] = $values[$name];
	}

	$form['fields'][] = array(
		'type' => 'actions',
		'fields' => array(
			array(
				'type' => 'submit',
				'label' => $id ? t('Edit') : t('Add'),
			),
			array(
				'title' => t('Cancel'),
				'path' => "scaffolding/{$table}/index",
				'view' => 'link',
				'attributes' => array('class' => array(
					'btn',
					'btn-danger',
				)),
			),
		),
	);

	return $form;
}

function scaffolding_handle_form($table, $action, $id, &$form) {
	global $schema;

	$callback = @$schema[$table]['custom form handle'];
	if(is_callable($callback)) {
		if($callback($form, $action, $id))
			return redirect("scaffolding/{$table}/index");
	}
	else {
		if(form_run($form) && form_is_valid($form)) {
			$form_values = form_values($form);
			try {

				if($id)
					scaffolding_set_item($table, $id, $form_values);
				else
					scaffolding_add_item($table, $form_values);

				return redirect("scaffolding/{$table}/index");

			} catch (Exception $e) {
				$form['error'] = $e->getMessage();
				$form['valid'] = FALSE;
			}
		}
	}
}

function scaffolding_get_table_menu($table) {
	global $schema;

	$table_menu = array(
		'items' => array(),
	);

	if(scaffolding_check_action($table, 'list'))
		$table_menu['items'][] = array(
				'title' => t('Table content'),
				'path' => 'scaffolding/'.$table.'/index',
		);

	if(scaffolding_check_action($table, 'add'))
		$table_menu['items'][] = array(
				'title' => t('Add content'),
				'path' => 'scaffolding/'.$table.'/add',
		);

	return $table_menu;
}

function scaffolding_get_tables_menu() {
	load_model('user');

	global $schema;

	$tables_menu = array(
		'items' => array(),
	);

	foreach ($schema as $table_name => $table_info) {
		if(!scaffolding_check_action($table_name, 'list'))
			continue;

		$tables_menu['items'][] = array(
			'title' => t(val($table_info['display name'], $table_name)),
			'path' => "scaffolding/{$table_name}",
		);
	}

	return $tables_menu;
}

function _scaffolding_file_field_save($table, $values) {
	global $schema;
	foreach ((array) @$schema[$table]['foreign keys'] as $fk) {
		if($fk['table'] === 'files') {
			load_model('file');
			reset($fk['columns']);
			while(list($field_name, $foreign_field) = each($fk['columns'])) {
				if($foreign_field === 'fid') {
					file_save($values[$field_name]);
				}
			}
		}
	}
}
