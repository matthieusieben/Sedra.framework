<?php

load_model('error');
load_model('theme');

function form_run(&$form) {
	global $request_uri;

	if (!@$form['id']) {
		# Try to build a default id for the form that won't change over runs
		$form['id'] = md5(var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), TRUE));
	}

	$form += array(
		'name' => '__form_'.$form['id'],
		'method' => 'post',
		'action' => $request_uri,
		'type' => 'form',
		'fields' => array(),
		'style' => 'horizontal', # horizontal, vertical, inline, search
		'title' => NULL,
	);

	$form['request'] = ($form['method'] === 'get') ? $_GET : $_POST;

	$form['attributes']['action'] = $form['action'];
	$form['attributes']['method'] = $form['method'];
	$form['attributes']['accept-charset'] = 'utf-8';
	$form['attributes']['enctype'] = 'application/x-www-form-urlencoded';

	$form['fields'][] = array(
		'type' => 'hidden',
		'name' => $form['name'],
		'value' => 1,
	);

	_form_run_field($form, $form);

	hook_invoke('form_run', $form);

	return $form['submitted'];
}

function _form_run_field(&$form, &$field, $name = NULL) {

	if(!is_array($field))
		throw new FrameworkException("Wrong form format");

	# Not a form field, do no prevent validation (return TRUE)
	if (empty($field['type'])) {
		return TRUE;
	}

	# Set default field values
	$field += array(
		'name' => is_numeric($name) ? "__form_field_{$name}" : $name,
		'attributes' => array(),
		'callback' => '_form_callback_'.$field['type'],
		'handler' => '_form_handle_'.$field['type'],
		'valid' => TRUE,
		# The following values should not be used on the form array
		'required' => FALSE,
		'default' => NULL,
		'label' => '',
		'help' => '',
		'prepend' => NULL,
		'append' => NULL,
		'error' => '',
		'placeholder' => NULL,
		'disabled' => FALSE,
		'multiple' => FALSE, # TODO : handle this for non-select fields
		'value' => NULL,
	);
	$field += array(
		'id' => $form['id'].'_'.$field['name'],
	);
	$field['attributes'] += array(
		'class' => array(),
		'name' => $field['name'],
		'id' => $field['id'],
	);
	$field['form'] = &$form;
	$field['style'] = &$form['style'];
	$field['submitted'] = isset($form['request'][$field['name']]);

	# Setup the value of non-form fields
	if ($field['type'] !== 'form') {

		$field['value'] = ($field['value'] !== NULL)
			? $field['value']
			: (($form['submitted'] && !$field['disabled']) ? @$form['request'][$field['name']] : $field['default']);

		if ($field['multiple']) {
			$field['value'] = (array) $field['value'];
			$field['attributes']['name'] .= '[]';
		} else {
			# Prevent hack
			if (is_array($field['value'])) {
				$field['valid'] = FALSE;
				$field['value'] = $field['default'];
			}
		}
	}

	if(!function_exists($field['callback'])) {
		$field['callback'] = NULL;
	}

	# Handle the field content
	if (function_exists($callback = $field['handler'])) {
		$callback($form, $field);
	}

	$field += array(
		'view' => 'form/'.$field['type'],
	);

	if ($form['submitted']) {
		$was_validated = FALSE;

		# Field specific action to validate
		if (function_exists($callback = $field['callback'])) {
			# Validate this field
			$valid = $callback($form, $field);
			if (is_bool($valid)) {
				$field['valid'] = $valid;
				$was_validated = TRUE;
			}
		}

		# Provide default validation
		if ($field['required'] && !$was_validated) {
			if (!$field['valid'] && !is_numeric($field['value'])) {
				# Mark the field as invalid if empty
				$field['valid'] = FALSE;
			}
		}

		# Provide defualt error message
		if(!$field['valid'] && empty($field['error'])) {
			$field['error'] = t('This field is required');
		}
	}

	# Handle & validate sub-fields
	if (!empty($field['fields']))
	foreach ($field['fields'] as $sub_name => &$sub_field) {
		if (!_form_run_field($form, $sub_field, $sub_name)) {
			$field['valid'] = FALSE;
		}
	}

	# Make sure 'attributes' has the right format
	attributes_setup($field['attributes']);

	# Propagate the validity
	return $field['valid'];
}

function form_is_valid($form) {
	return $form['valid'];
}

function form_values(&$form) {
	if (!isset($form['values'])) {
		function _form_values(&$form, $field) {
			if(!empty($field['fields'])) {
				foreach ($field['fields'] as $sub_field) {
					if (!empty($sub_field['type'])) {
						# Handle the field content
						if ($sub_field['valid']) {
							if (strpos($sub_field['name'], '__form_') !== 0) {
								$form['values'][$sub_field['name']] = $sub_field['value'];
							}
						} else {
							$form['values'][$sub_field['name']] = $sub_field['default'];
						}

						_form_values($form, $sub_field);
					}
				}
			}
		}
		_form_values($form, $form);
	}
	return $form['values'];
}

function form_defaults(&$form) {
	if (!isset($form['defaults'])) {
		function _form_defaults(&$form, &$field) {
			if(!empty($field['fields'])) {
				foreach ($field['fields'] as $sub_field) {
					if(!empty($sub_field['type'])) {
						# Handle the field content
						if ($sub_field['name'] && (strpos($sub_field['name'], '__form_') !== 0)) {
							$form['defaults'][$sub_field['name']] = $sub_field['default'];
						}
						_form_defaults($form, $sub_field);
					}
				}
			}
		}
		_form_defaults($form, $form);
	}
	return $form['defaults'];
}

function form_set(&$form, array $values) {
	function field_set(&$form, &$field) {
		if (array_key_exists($field['name'], $form['values'])) {
			$field['value'] = $form['values'][$field['name']];
		}
		if(!empty($field['fields'])) {
			foreach ($field['fields'] as &$sub_field) {
				if(!empty($sub_field['type'])) {
					field_set($form, $sub_field);
				}
			}
		}
	}
	$form['values'] = $values + (array) @$form['values'];
	field_set($form, $form);
}

function form_reset(&$form) {
	form_set($form, form_defaults($form));
	unset($form['values']);
}

function _form_handle_checkbox(&$form, &$field) {

	if ($field['multiple'])
		$field['attributes']['type'] = 'checkbox';
	else
		$field['attributes']['type'] = 'radio';

	if ($field['disabled'])
		$field['attributes']['disabled'] = 'disabled';

	_form_handle_multiple_field($form, $field);
}

function _form_handle_select(&$form, &$field) {
	if ($field['disabled'])
		$field['attributes']['disabled'] = 'disabled';
	else if ($field['required'])
		$field['attributes']['required'] = '';

	if ($field['multiple'])
		$field['attributes']['multiple'] = 'multiple';
	else
		# Add default empty value if not required
		if (!$field['required'] || is_null($field['default']))
			$field['options'] += array(NULL => '-');


	_form_handle_multiple_field($form, $field);
}

function _form_handle_multiple_field(&$form, &$field) {
	if ($field['multiple']) {
		foreach ($field['value'] as $_k => $_value) {
			# Remove unproposed values
			if (!isset($field['options'][$_value])) {
				unset($field['value'][$_k]);
			}
			# Replace empty string by NULL
			elseif ($_value === '') {
				$field['value'][$_k] = NULL;
			}
		}
	}
	# Not a multiple field
	else {
		# Reset invalid value (prevent hack)
		if (!isset($field['options'][$field['value']])) {
			$field['valid'] = FALSE;
			$field['value'] = $field['default'];
		}

		# Replace empty string by NULL
		if ($field['value'] === '')
			$field['value'] = NULL;
	}
}

function _form_handle_submit(&$form, &$field) {
	static $is_first = TRUE;
	$field['is_first'] = $is_first;
	$is_first = FALSE;

	$field += array(
		'view' => 'form/submit',
	);
	$field['attributes']['type'] = 'submit';

	if (empty($field['value']))
		# $field['label'] is plain HTML.
		$field['attributes']['value'] = decode_plain(strip_tags($field['label']));
	else
		$field['attributes']['value'] = strval($field['value']);

	if ($field['disabled'])
		$field['attributes']['disabled'] = 'disabled';
}

function _form_handle_button(&$form, &$field) {
	$field += array(
		'view' => 'form/button',
	);
	$field['attributes'] += array(
		'type' => $field['type'],
	);
	if ($field['disabled']) {
		$field['attributes']['disabled'] = 'disabled';
	}
}

function _form_handle_file(&$form, &$field) {
	load_model('file');

	# Setup defaults
	$field += array(
		'view' => 'form/file',
		'file_info' => NULL,
	);
	$field['attributes'] += array(
		'type' => $field['type'],
		'required' => $field['required'] ? '' : NULL,
	);
	$form['attributes']['enctype'] = 'multipart/form-data';
	if ($field['disabled']) {
		$field['attributes']['disabled'] = 'disabled';
	}
	if (empty($field['mime'])) {
		$field['mime'] = array('text', 'image', 'application/pdf');
	}
	if (!$field['callback']) {
		$field['callback'] = '_form_callback_file';
	}

	# Only process file if a name is defined
	if ($form['submitted'] && $field['name'] && !$field['disabled']) {
		if ($field['file_info'] = file_upload($field['name'], $field['value'])) {
			# A new file was uploaded
			$field['value'] = $field['file_info']['fid'];
		} elseif ($field['file_info'] = file_info(array('fid' => $field['value']))) {
			# The file was already submitted and is still in the database.
			$field['value'] = $field['file_info']['fid'];
		} else {
			$field['value'] = NULL;
			$field['file_info'] = NULL;
		}
	}
}

function _form_handle_textarea(&$form, &$field) {
	$field['value'] = trim($field['value']);
	$field += array(
		'view' => 'form/input',
		'wysiwyg' => FALSE,
		'html' => FALSE,
		'allowable_tags' => '<br><span><p><div><i><b><ul><ol><li><dl><dt><dd>',
	);
	$field['attributes'] += array(
		'rows' => 3,
	);
	if ($field['required']) {
		$field['attributes']['required'] = '';
	}
	if ($field['wysiwyg']) {
		$field['html'] = TRUE;
	}
	if ($field['html'] && !$field['wysiwyg']) {
		$field['value'] = nl2br($field['value']);
	}
	if ($field['html'] && $field['allowable_tags']) {
		$field['value'] = strip_tags($field['value'], $field['allowable_tags']);
	}
	if ($field['disabled']) {
		$field['attributes']['disabled'] = 'disabled';
	}
	if ($field['wysiwyg']) {
		load_library('wysiwyg');
		$field['attributes']['class'][] = 'wysiwyg';
		$field['attributes']['style'][] = 'height: 150px;';
	}
}

function _form_handle_email(&$form, &$field) {
	if (!$field['callback']) {
		$field['callback'] = '_form_callback_email';
	}
	_form_handle_text($form, $field);
}

function _form_handle_text(&$form, &$field) {
	$field['value'] = trim($field['value']);
	$field['attributes']['type'] = 'text';
	_form_handle_input($form, $field);
}

function _form_handle_password(&$form, &$field) {
	$field['value'] = strval($field['value']);
	_form_handle_input($form, $field);
	$field['attributes']['value'] = NULL;
}

function _form_handle_hidden(&$form, &$field) {
	_form_handle_input($form, $field);
}

function _form_handle_number(&$form, &$field) {
	if(!is_null($field['value']) && $field['value'] !== '') {
		$field['value'] = floatval($field['value']);
		if(is_numeric(@$field['round']) && is_numeric($field['value']))
			$field['value'] = round($field['value'], $field['round']);
		$field['attributes']['value'] = strval($field['value']);
	} else {
		$field['value'] = NULL;
	}
	$field['attributes']['type'] = 'text';
	_form_handle_input($form, $field);
}

function _form_handle_input(&$form, &$field) {
	$field += array(
		'view' => 'form/input',
	);
	$field['attributes'] += array(
		'type' => $field['type'],
		'value' => $field['value'],
		'placeholder' => decode_plain($field['label']),
	);
	if ($field['required']) {
		$field['attributes']['required'] = '';
	}
	if ($field['disabled']) {
		$field['attributes']['disabled'] = 'disabled';
	}
	if ($field['placeholder']) {
		$field['attributes']['placeholder'] = t($field['placeholder']);
	}
}

function _form_callback_file(&$form, &$field) {
	load_model('file');

	if ($field['file_info']) {
		foreach ((array) @$field['mime'] as $mime) {
			if (strpos($field['file_info']['type'], $mime) === 0) {
				return TRUE;
			}
		}
		if (!$field['error'])
			$field['error'] = t('You are not allowed to upload this kind of file.');
		return FALSE;
	}

	return TRUE;
}

function _form_callback_email(&$form, &$field) {
	if (!is_email($field['value'])) {
		$field['error'] = t('This is not a valid email address.');
		return FALSE;
	}
	return TRUE;
}

function _form_callback_user_password(&$form, &$field) {
	load_model('user');

	if($field['required'] || $field['value']) {
		if (!user_check_password($field['value'], $field['error'])) {
			return FALSE;
		}
	}

	return TRUE;
}

function _form_callback_number(&$form, &$field) {
	if ($field['required'] && !is_numeric($field['value'])) {
		if (!$field['error'])
			$field['error'] = t('This value is expected to be numeric.');
		return FALSE;
	}
	if (isset($field['min'])) {
		if ($field['value'] && $field['value'] < $field['min']) {
			if (!$field['error'])
				$field['error'] = t('This value is too small.');
			return FALSE;
		}
	}
	if (isset($field['max'])) {
		if ($field['value'] && $field['value'] > $field['max']) {
			if (!$field['error'])
				$field['error'] = t('This value is too large.');
			return FALSE;
		}
	}
	return TRUE;
}

function _form_callback_text(&$form, &$field) {
	if (isset($field['min'])) {
		if (strlen($field['value']) < $field['min']) {
			if (!$field['error'])
				$field['error'] = t('This value is expected to be at least @num char long.', array('@num' => $field['min']));
			return FALSE;
		}
	}
	if (isset($field['max'])) {
		if (strlen($field['value']) > $field['max']) {
			if (!$field['error'])
				$field['error'] = t('This value is expected to be at most @num char long.', array('@num' => $field['max']));
			return FALSE;
		}
	}
	if (isset($field['regex'])) {
		if (!preg_match($field['regex'], $field['value'])) {
			return FALSE;
		}
	}
	return $field['required'] ? !empty($field['value']) : TRUE;
}

function _form_callback_textarea(&$form, &$field) {
	return _form_callback_text($form, $field);
}
