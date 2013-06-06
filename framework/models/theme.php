<?php

require_once 'message.php';
require_once 'data.php';

# The following file should be loaded from their respective function but
# due to the modification of the include path by theme(), the wrong files
# could be included upon calling these 'require' directives.

require_once 'file.php';
require_once 'avatar.php';

define('VIEW_PATH', APP_VIEWS.PATH_SEPARATOR.FRAMEWORK_VIEWS.PATH_SEPARATOR.'.');

function attributes($attributes = array(), $filter_empty = FALSE) {
	if(empty($attributes) || !is_array($attributes))
		return '';

	foreach ($attributes as $attribute => &$data) {
		if($filter_empty && empty($data))
			continue;

		$data = implode(' ', (array) $data);
		$data = $attribute . '="' . check_plain($data) . '"';
	}
	return ' ' . implode(' ', $attributes);
}

function attributes_setup(array &$attributes) {
	# Clear empty attributes
	foreach($attributes as $name => $value) {
		if(empty($value)) {
			unset($attributes[$name]);
		}
	}
	# Make sure 'class' is an array
	$attributes['class'] = (array) @$attributes['class'];
}

function l(array $options) {
	url_setup($options);
	attributes_setup($options['attributes']);

	if(!empty($options['active'])) {
		$options['attributes']['class'][] = 'active';
	}

	switch(@$options['target']) {
	case NULL:
		break;
	case 'blank':
	case 'self':
	case 'parent':
	case 'top':
		$options['attributes']['target'] = '_'.$options['target'];
		break;
	default:
		$options['attributes']['target'] = $options['target'];
		break;
	}

	if (!isset($options['attributes']['title'])) {
		$options['attributes']['title'] = $options['title'];
	}

	if (strpos($options['attributes']['title'], '<') !== FALSE) {
		$options['attributes']['title'] = strip_tags($options['attributes']['title']);
	}

	$options['attributes']['href'] = url($options);

	return '<a' . attributes($options['attributes']) . '>' . ($options['html'] ? $options['title'] : check_plain($options['title'])) . '</a>';
}

function theme_data(array $data) {
	global $site_data, $user;

	$data += (array) $site_data;

	if(!isset($data['page_title'])) {
		if(isset($data['title'])) {
			$data['page_title'] = $data['site_name'] . ' - ' . $data['title'];
		}
		else {
			$data['page_title'] = $data['site_name'];
		}
	}

	return $data;
}

function theme($__view, array $__data = array()) {
	if(is_array($__view)) {
		$__data = $__view;
		$__view = @$__data['view'];
	}

	# Avoid file name conflicts
	$__current_include_path = get_include_path();
	set_include_path(VIEW_PATH);

	try {
		# Resolve the file
		$__view = strtolower($__view);
		$__file = stream_resolve_include_path($__view.'.php');
		if (!$__file) {
			set_include_path($__current_include_path);
			throw new FrameworkException(t("The view <code>@view</code> cannot be loaded.", array('@view' => $__view ? $__view : 'NULL')));
		}

		# Add default data & set them available
		extract(theme_data($__data));

		ob_start();
		include $__file;
		$__html = ob_get_clean();
	}
	catch(Exception $e) {
		set_include_path($__current_include_path);
		throw $e;
	}

	set_include_path($__current_include_path);

	return $__html;
}

function theme_user_text(array $data) {

	$data += array(
		'text' => NULL,
		'markup' => NULL,
		'length' => NULL,
		'lines' => NULL,
	);

	$text = $data['text'];

	switch($data['markup']) {
	case 'html':
		break;
	case 'markdown':
		# TODO : support md
	default:

		if (is_numeric($data['lines'])) {
			for($i = 0, $j = (int) $data['lines']; $i !== FALSE && $j > 0; $i = strpos($text, "\n", $i + 1), $j--);
			if ($i !== FALSE && $j === 0) $text = substr($text, 0, $i);
		}

		if (is_numeric($data['length']) && strlen($text) > $data['length'] && $data['length'] > 3) {
			# TODO : better cut (not cutting the last word or html tags).
			$text = substr($text, 0, $data['length'] - 3) . '...';
		}

		$text = check_plain($text);
		$text = nl2br($text, TRUE);

		break;
	}

	return $text;
}

function theme_date($timestamp) {
	$time_ago = REQUEST_TIME - $timestamp;

	if ($time_ago >= 0) {
		# In the past
		if (($years = (int) ($time_ago / 31536000)) > 1) {
			$text = t('@years years ago', array('@years' => $years));
		}
		elseif ($years === 1) {
			$text = t('one year ago');
		}
		elseif (($months = (int) ($time_ago / 2678400)) > 1) {
			$text = t('@months months ago', array('@months' => $months));
		}
		elseif ($months ===  1) {
			$text = t('one month ago');
		}
		elseif (($weeks = (int) ($time_ago / 604800)) > 1) {
			$text = t('@weeks weeks ago', array('@weeks' => $weeks));
		}
		elseif ($weeks ===  1) {
			$text = t('one week ago');
		}
		elseif (($days = (int) ($time_ago / 86400)) > 1) {
			$text = t('@days days ago', array('@days' => $days));
		}
		elseif ($days ===  1) {
			$text = t('one day ago');
		}
		elseif (($hours = (int) ($time_ago / 3600)) > 1) {
			$text = t('@hours hours ago', array('@hours' => $hours));
		}
		elseif ($hours ===  1) {
			$text = t('one hour ago');
		}
		elseif (($minutes = (int) ($time_ago / 60)) > 1) {
			$text = t('@minutes minutes ago', array('@minutes' => $minutes));
		}
		elseif ($minutes ===  1) {
			$text = t('one minute ago');
		}
		else {
			$text = t('just now');
		}
	} else {
		# In the future
		$text = check_plain(strftime('%c', $timestamp));
	}

	return '<span>'.$text.'</span>';
}

function theme_file($fid, $thumbnail = TRUE, $as_link = TRUE) {

	if ($file_info = file_info($fid)) {
		return theme('file', array(
			'file_info' => $file_info,
			'thumbnail' => $thumbnail,
			'thumbnail_link' => $as_link,
		));
	} else {
		return t('File not found (404)');
	}
}

function theme_avatar($account, $size = 256, $caption = NULL) {

	$data = array(
		'account' => $account,
		'size' => $size,
		'mini' => $size <= 32,
		'caption' => $caption,
		'avatar_url' => avatar_url($account, (int) $size),
	);

	return theme('account/avatar', $data);
}

function theme_css($css_file, $media = NULL) {
	if($file_url = file_url($css_file)) {
		$media_attr = $media ? ' media="'.$media.'"' : '';
		return '<link href="'.$file_url.'" rel="stylesheet" type="text/css"'.$media_attr.'>';
	}
	else {
		return NULL;
	}
}

function theme_js($js_file) {
	if($file_url = file_url($js_file)) {
		return '<script src="'.$file_url.'"></script>';
	}
	else {
		return NULL;
	}
}
