<?php

function a($page, $title) {
	echo '<a href="'.Url::make($page).'" title="'.$title.'">'.html($title).'</a>';
}

function css($file, $media = 'screen')
{
	if($href = Theme::css($file))
		echo '<link rel="stylesheet" type="text/css" href="'.$href.'" media="'.$media.'" />';
}

function js($file, $charset = 'utf-8')
{
	if($src = Theme::js($file))
		echo '<script type="text/javascript" charset="'.$charset.'" src="'.$src.'"></script>';
}
