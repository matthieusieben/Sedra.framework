<?php

function hook_less_alter_url($_, $url) {
	$pathinfo = pathinfo($url);
	if(strToLower(@$pathinfo['extension']) === 'less') {
		return Url::make('less/' . $pathinfo['basename']);
	}
	return $url;
}

Hook::register(HOOK_URL_FILE, 'hook_less_alter_url');
