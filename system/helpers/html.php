<?php

function css($file, $media = 'screen')
{
	if($href = Url::css($file))
		echo '<link rel="stylesheet" type="text/css" href="'.$href.'" media="'.$media.'" />';
}

function js($file, $charset = 'utf-8')
{
	if($src = Url::js($file))
		echo '<script type="text/javascript" charset="'.$charset.'" src="'.$src.'"></script>';
}
