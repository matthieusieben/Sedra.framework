<?php

function css($file, $media = 'screen')
{
	echo '<link rel="stylesheet" type="text/css" href="'.Url::css($file).'" media="'.$media.'" />';
}

function js($file)
{
	echo '<script type="text/javascript" charset="utf-8" src="'.Url::js($file).'"></script>';
}
