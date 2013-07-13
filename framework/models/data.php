<?php

load_model('menu');

# uses
global $language;
global $breadcrumb;
global $menus;
# provides
global $data;

$data = array(
	'lang' => $language,
	'breadcrumb' => &$breadcrumb,
	'menus' => &$menus,
	'site_name' => config('site.name', 'Sedra Framework'),
);

# Get data
function &data($key = NULL, $default = NULL) {
	global $data;
	hook_invoke('site data', $data);
	if(isset($key)) {
		if(isset($data[$key]))
			return $data[$key];
		else {
			return $default;
		}
	}
	return $data;
}
