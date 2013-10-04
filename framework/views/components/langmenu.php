<?php global $request_path; ?>
<?php global $language; ?>
<?php $_languages = language_list(); ?>
<?php $_lang_menu = array('items' => array()); ?>
<?php foreach($_languages as $_code => $_name) {
	$_lang_menu['items'][] = array(
		'path' => $request_path,
		'query' => array('language' => $_code),
		'title' => $_name,
		'active' => $language === $_code,
	);
} ?>
<?php echo theme('components/menu', $_lang_menu); ?>