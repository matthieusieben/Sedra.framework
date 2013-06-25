<?php

function menu_add($menu_id, $link) {
	load_model('data.php');
	global $site_data;
	$site_data['menus'][$menu_id]['items'][] = $link;
}
