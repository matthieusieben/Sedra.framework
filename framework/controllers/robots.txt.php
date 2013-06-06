<?php

require_once 'data.php';
global $private_pages;

set_status_header(200);
header('Content-Type: text/plain; charset=utf-8');

$dissalowed = config('robots.dissalowed', array());

hook_invoke('robots.txt', $dissalowed);

if(empty($dissalowed) || empty($private_pages))
	return NULL;

foreach((array) $dissalowed as $robot) {
	$content = "User-Agent: ${robot}\n";
	foreach ($private_pages as $page)
		$content .= "Disallow: ${page}\n";
}

return $content;