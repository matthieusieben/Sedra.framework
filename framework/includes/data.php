<?php

require_once 'includes/menu.php';

# uses
global $language;
global $breadcrumb;
# provides
global $data;

$data = array(
	'lang' => $language,
	'menus' => menu_get(),
	'breadcrumb' => &$breadcrumb,
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
