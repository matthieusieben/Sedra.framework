<?php

Load::model('cms');

# Get the theme name 
if($theme = CMS::setting('theme')) {
	Load::add_view_path('themes/'.$theme.'/');
}
