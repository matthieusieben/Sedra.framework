<?php

require_once 'includes/schema.php';

function scaffolding_get_id($table, $id) {
	$table_info = schema_get($table);

	if(!is_array($id))
		$id = explode(',', $id);

	$pri = (array) @$table_info['primary key'];

	if(empty($id) || count($id) != count($pri))
		return NULL;

	$id = array_combine($pri, $id);

	return $id;
}

function scaffolding_get_items($table, $cond) {
	$table_info = schema_get($table);

	$query = db_select($table, 't')->fields('t');

	if(!empty($cond))
		foreach($cond as $_col => $_value)
			$query->condition($_col, $_value);

	foreach ((array) @$table_info['order'] as $field => $order)
		$query->orderBy($field, $order);

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
	$table_info = schema_get($table);
	$values = array_intersect_key($values, $table_info['fields']);

	$r = db_insert($table)
		->fields($values)
		->execute();

	_scaffolding_file_field_save($table, $values);

	if(!empty($table_info['menu']['path'])) {
		$menu_item = $table_info['menu'];
		$replacements = array();
		foreach($values as $key => $value)
			$replacements["!{$key}"] = $value;
		if($cond = scaffolding_get_id($table, $r))
			foreach($cond as $key => $value)
				$replacements["!{$key}"] = $value;
		foreach($menu_item as $key => &$value)
			$value = strtr($value, $replacements);
		menu_add($menu_item);
	}

	return $r;
}

function scaffolding_set_item($table, $id, $values) {
	$table_info = schema_get($table);

	$cond = scaffolding_get_id($table, $id);
	if(empty($cond))
		return FALSE;

	$query = db_update($table)
		->fields(array_intersect_key($values, $table_info['fields']));

	foreach($cond as $_col => $_value)
		$query->condition($_col, $_value);

	$r = $query->execute();

	_scaffolding_file_field_save($table, $values);

	return $r;
}

function scaffolding_delete_id($table, $id) {
	$table_info = schema_get($table);

	if(empty($id))
		return NULL;

	$cond = scaffolding_get_id($table, $id);
	if(empty($cond))
		return FALSE;

	$query = db_delete($table);
	foreach($cond as $_col => $_value)
		$query->condition($_col, $_value);
	$query->execute();

	if(!empty($table_info['menu']['path'])) {
		$replacements = array();
		foreach($cond as $key => $value)
			$replacements["!{$key}"] = $value;
		menu_delete(strtr($table_info['menu']['path'], $replacements));
	}

	return TRUE;
}

function scaffolding_list_query($table, $start = NULL, $limit = NULL) {
	$table_info = schema_get($table);
	$query = db_select($table, 't')->fields('t');
	if(is_numeric($limit))
		$query->range((int) $start, (int) $limit);
	foreach ((array) @$table_info['order'] as $field => $order)
		$query->orderBy($field, $order);
	return $query;
}

function scaffolding_list($table, $start = NULL, $limit = NULL) {
	$query = scaffolding_list_query($table, $start, $limit);
	return $query->execute();
}

function scaffolding_check_action($table, $action) {
	if($table) {
		$table_info = schema_get($table);
		if(isset($table_info['roles'][$action]))
			if(user_has_role($table_info['roles'][$action]))
				return TRUE;
		return FALSE;
	} else {
		# Check for any table
		foreach (schema_get_all() as $table_info)
			if(isset($table_info['roles'][$action]))
				if(user_has_role($table_info['roles'][$action]))
					return TRUE;
		return FALSE;
	}
}

function scaffolding_get_edit_form($table, $action, $id, $values = NULL) {
	$table_info = schema_get($table);

	if(isset($table_info['custom form'])) {
		$form = $table_info['custom form'];
	}
	else {
		$form = array('fields' => array());
		foreach($table_info['fields'] as $field_name => $field_info) {

			if(@$field_info['hidden'])
				continue;

			if(!empty($field_info['options']))
				$form_field = array(
					'type' => 'select',
					'default' => @$field_info['default'],
					'options' => $field_info['options'],
				);
			else
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

		foreach((array) @$table_info['foreign keys'] as $fk) {
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

function scaffolding_handle_form_default($table, $action, $id, &$form) {
	if($action === 'add' || $action === 'edit') {
		if(form_run($form) && form_is_valid($form)) {
			$form_values = form_values($form);
			try {

				if($id)
					scaffolding_set_item($table, $id, $form_values);
				else
					scaffolding_add_item($table, $form_values);

				return TRUE;

			} catch (Exception $e) {
				$form['error'] = $e->getMessage();
				$form['valid'] = FALSE;
			}
		}
		return $form['submitted'] && $form['valid'];
	}
	else if ($action === 'remove') {
		return scaffolding_delete_id($table, $id);
	}
	return TRUE;
}

function scaffolding_handle_form($table, $action, $id, &$form) {
	require_once 'includes/form.php';

	$table_info = schema_get($table);
	$callback = @$table_info['custom form handle'];

	if($callback) {
		# Make sure the callback is defined
		require_once "schemas/{$table}.php";
		if(!is_callable($callback)) {
			throw new FrameworkException(t('Scaffolding unable to find function &laquo;@callback&raquo;.', array('@callback' => $callback)), 500);
		}
	}
	else {
		$callback = 'scaffolding_handle_form_default';
	}

	if($callback($table, $action, $id, $form)) {
		return redirect("scaffolding/{$table}");
	}

	return FALSE;
}

function scaffolding_get_table_menu($table) {

	$table_menu = array(
		'items' => array(),
	);

	if(scaffolding_check_action($table, 'list'))
		$table_menu['items'][] = array(
				'title' => t('Table content'),
				'path' => 'scaffolding/'.$table.'',
		);

	if(scaffolding_check_action($table, 'add'))
		$table_menu['items'][] = array(
				'title' => t('Add content'),
				'path' => 'scaffolding/'.$table.'/add',
		);

	return $table_menu;
}

function scaffolding_get_tables_menu() {
	global $user, $language;
	$cache_id = "scaffolding_menu/{$language}/{$user->rid}";

	if(!($tables_menu = cache_get($cache_id))) {
		$tables_menu = array(
			'items' => array(),
		);

		foreach (schema_get_all() as $table_name => $table_info) {
			if(!scaffolding_check_action($table_name, 'list'))
				continue;

			$tables_menu['items'][] = array(
				'title' => t(val($table_info['display name'], $table_name)),
				'path' => "scaffolding/{$table_name}",
			);
		}

		cache_set($cache_id, $tables_menu);
	}

	return $tables_menu;
}

function _scaffolding_file_field_save($table, $values) {
	$table_info = schema_get($table);
	foreach ((array) @$table_info['foreign keys'] as $fk) {
		if($fk['table'] === 'files') {
			require_once 'includes/file.php';
			reset($fk['columns']);
			while(list($field_name, $foreign_field) = each($fk['columns'])) {
				if($foreign_field === 'fid') {
					file_save($values[$field_name]);
				}
			}
		}
	}
}
