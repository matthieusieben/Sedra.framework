<?php

function attributes($attributes = array()) {
	if(empty($attributes) || !is_array($attributes))
		return '';

	foreach ($attributes as $attribute => &$data) {
		if($data === FALSE)
			continue;

		if(!$data && !is_numeric($data)) {
			$data = check_plain($attribute);
		} else {
			if(is_array($data))
				$data = implode(' ', $data);
			$data = check_plain($attribute) . '="' . check_plain($data) . '"';
		}
	}
	return ' ' . implode(' ', $attributes);
}

function attributes_setup(array &$attributes) {
	# Clear empty attributes
	foreach($attributes as $name => $value) {
		if($value === FALSE) {
			unset($attributes[$name]);
		}
	}
	# Make sure 'class' is an array
	$attributes['class'] = (array) @$attributes['class'];
}

function l(array $options) {
	url_setup($options);
	attributes_setup($options['attributes']);

	if(@$options['active']) {
		$options['attributes']['class'][] = 'active';
	}

	if(@$options['disabled']) {
		$options['attributes']['class'][] = 'disabled';
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

	$content = $options['html'] ? $options['title'] : check_plain($options['title']);

	if(@$options['disabled'])
		return $content;
	else
		return '<a' . attributes($options['attributes']) . '>' . $content . '</a>';
}

function theme_data(array $__data) {
	require_once 'includes/data.php';

	hook_invoke('user data', $__data);

	$__data += data();

	if(!isset($__data['page_title'])) {
		if(isset($__data['title'])) {
			$__data['page_title'] = $__data['site_name'] . ' - ' . $__data['title'];
		}
		else {
			$__data['page_title'] = $__data['site_name'];
		}
	}

	return $__data;
}

function theme($__view, array $__data = array()) {
	return load_view($__view, $__data);
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

	if(!is_numeric($timestamp))
		$timestamp = strtotime($timestamp);

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

	return '<span class="date">'.$text.'</span>';
}

function theme_file($file_info, $thumbnail = TRUE, $as_link = TRUE) {
	require_once 'includes/file.php';

	if(!is_array($file_info))
		$file_info = file_info(array('fid' => $file_info));

	if ($file_info) {
		return theme('components/file', array(
			'file_info' => $file_info,
			'thumbnail' => $thumbnail,
			'thumbnail_link' => $as_link,
		));
	} else {
		return t('File not found (404)');
	}
}

function theme_avatar($account, $size = 256) {
	if(load_module('gravatar', FALSE)) {

		if(is_numeric($account))
			$account = user_find(array('uid' => $account));
		else if(is_string($account))
			$account = user_find(array('mail' => $account));

		if(!$account instanceof User)
			return NULL;

		$data = array(
			'account' => $account,
			'size' => $size,
			'mini' => $size <= 32,
			'avatar_url' => gravatar_avatar( $account->mail, (int) $size),
		);

		return theme('account/avatar', $data);
	}
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
