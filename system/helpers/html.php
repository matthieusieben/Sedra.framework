<?php

function a($page, $title) {
	echo '<a href="'.Url::make($page).'" title="'.$title.'">'.html($title).'</a>';
}

function css($file, $media = 'screen')
{
	$href = $file;
	if(!is_url($file)) {
		$href = Theme::css($file);
		if(!$href) $href = Url::file($file);
	}

	if($href) {
		echo '<link rel="stylesheet" type="text/css" href="'.$href.'" media="'.$media.'" />';
	}
}

function js($file, $charset = 'utf-8')
{
	$src = $file;
	if(!is_url($file)) {
		$src = Theme::js($file);
		if(!$src) $src = Url::file($file);
	}

	if($src) {
		echo '<script type="text/javascript" charset="'.$charset.'" src="'.$src.'"></script>';
	}
}
