<?php

require_once 'includes/menu.php';

# Uses
global $language;
global $breadcrumb;
# Provides
global $data;

$data = array(
	'lang' => &$language,
	'menus' => menus_get(),
	'breadcrumb' => &$breadcrumb,
	'site_name' => config('site.name', 'Sedra Framework'),
);

hook_invoke('site data', $data);

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
		// Not a problem if mutiple times the same class
		// if(!in_array('active', $options['attributes']['class']))
		{
			$options['attributes']['class'][] = 'active';
		}
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

	$content = @$options['html'] ? $options['title'] : check_plain($options['title']);

	if(@$options['disabled']) {
		$options['attributes']['disabled'] = 'disabled';
	}

	return '<a' . attributes($options['attributes']) . '>' . $content . '</a>';
}

function theme_data(array $__data) {
	global $data;

	$__data += $data;

	hook_invoke('user data', $__data);

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
	if(is_array($__view)) {
		$__data += $__view;
		$__view = @$__data['view'];
	}

	if(empty($__view)) {
		return NULL;
	}

	return load_view($__view, theme_data($__data));
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

	if(is_numeric($account))
		$account = user_find(array('uid' => $account));
	else if(is_string($account))
		$account = user_find(array('mail' => $account));

	if(!$account instanceof User)
		return NULL;

	return theme('account/avatar', array(
		'account' => $account,
		'size' => $size,
		'mini' => $size <= 32,
		'avatar_url' => theme_avatar_url($account, (int) $size),
	));
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

function theme_avatar_url($account, $s = 160) {
	$email = is_string($account) ? $account : $account->mail;

	// $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	$d = config('gravatar.default', 'mm');

	// $r Maximum rating (inclusive) [ g | pg | r | x ]
	$r = config('gravatar.rating', 'g');

	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5(strtolower(trim($email)));
	$url .= "?s=$s&d=$d&r=$r";

	return $url;
}
