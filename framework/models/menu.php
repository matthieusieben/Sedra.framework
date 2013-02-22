<?php

require_once 'data.php';

function menu_add($menu_id, $link) {
	global $site_data;
	$site_data['menus'][$menu_id]['items'][] = $link;
}
