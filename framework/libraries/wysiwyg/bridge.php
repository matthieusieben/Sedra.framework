<?php

load_library('jquery');

hook_register('html_foot', function () {
	global $language;
	$locales = config('site.locales', array('en' => 'en_US', 'fr' => 'fr_FR'));
	$locale = NULL;
	if(isset($locales[$language])) {
		$locale = strtr($locales[$language], '_', '-');
		$p = strpos($locale, '.');
		if($p !== FALSE)
			$locale = substr($locale, 0, $p);
	}

	$locale_file = 'libraries/wysiwyg/bootstrap-wysihtml5/src/locales/bootstrap-wysihtml5.'.$locale.'.js';
	$locale_js = theme_js($locale_file);

	$css = file_url('libraries/wysiwyg/bootstrap-wysihtml5/lib/css/wysiwyg-color.css');
	echo theme_css('libraries/wysiwyg/bootstrap-wysihtml5/src/bootstrap-wysihtml5.css');

	echo theme_js('libraries/wysiwyg/bootstrap-wysihtml5/lib/js/wysihtml5-0.3.0.min.js');
	echo theme_js('libraries/wysiwyg/bootstrap-wysihtml5/src/bootstrap-wysihtml5.js');

	if($locale_js) {
		echo $locale_js;
		echo <<<EOS
<script>
	$("textarea.wysiwyg").wysihtml5({
		"color": false,
		"html": true,
		"stylesheets": ["{$css}"],
		locale: "{$locale}"
	});
</script>
EOS;
	} else {
		echo <<<EOS
<script>
	$("textarea.wysiwyg").wysihtml5({
		"color": false,
		"html": true,
		"stylesheets": ["{$css}"]
	});
</script>
EOS;
	}
});
