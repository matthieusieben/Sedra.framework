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

function scaffolding_get_item($table, $id) {
	global $schema;

	if(empty($schema[$table]))
		return NULL;

	$cond = scaffolding_get_id($table, $id);
	if(empty($cond))
		return NULL;

	$query = db_select($table, 't')
		->fields('t');

	foreach($cond as $_col => $_value)
		$query->condition($_col, $_value);

	$item = $query
		->execute()
		->fetchAssoc();

	if(!$item)
		return NULL;

	return $item;
}

function scaffolding_add_item($table, $values) {
	global $schema;

	if(empty($schema[$table]['fields']))
		return FALSE;

	return db_insert($table)
		->fields(array_intersect_key($values, $schema[$table]['fields']))
		->execute();
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

	return $query->execute();
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

	if(isset($schema[$table]['roles'][$action]))
		return user_has_role($schema[$table]['roles'][$action]);

	return user_has_role(ADMINISTRATOR_RID);
}

function scaffolding_get_form($table, $action, $id, $values = NULL) {
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
				$type = 'textarea';
				break;
			case 'varchar':
				$type = 'text';
				break;
			case 'tinyint':
			case 'float':
			case 'int':
				$type = 'number';
				break;
			}

			if(empty($type))
				continue;

			$form['fields'][$field_name] = array(
				'label' => val($field_info['display name'], $field_name),
				'type' => $type,
				'required' => @$field_info['not null'],
				'wysiwyg' => $type === 'textarea',
				'allowable_tags' => NULL, # No restriction
				'default' => @$field_info['default'],
				'help' => t(@$field_info['description']),
			);
		}

		foreach((array) @$schema[$table]['foreign keys'] as $fk) {

			reset($fk['columns']);
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
		if($callback($form))
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
