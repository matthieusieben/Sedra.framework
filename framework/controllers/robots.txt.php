<?php

set_status_header(200);
header('Content-Type: text/plain; charset=utf-8');

$dissalowed = array();

hook_invoque('robots.txt', $dissalowed);

$content = 'User-Agent: *';

foreach ($dissalowed as $page) {
	$content .= "\nDisallow: " . $page;
}

return $content;