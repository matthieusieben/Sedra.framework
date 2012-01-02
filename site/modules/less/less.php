<?php

/**
 * Modify url to less files so that they use the Less controller
 *
 * @param string $url 
 * @return void
 */
function less_alter_file_url($url) {
	$pathinfo = pathinfo($url);
	if(strToLower(@$pathinfo['extension']) === 'less') {
		$url = Url::make('less/' . $pathinfo['basename']);
	}
}