<?php

require_once 'file.php';

if (headers_sent()) {
	throw new FrameworkException(t('HTTP Headers already sent while trying to output a file.'));
}

$fid = url_segment(1);
$info = file_info($fid);
if (!$info) {
	show_404();
}

hook_invoke('file', $info);

set_status_header(200);

header('Content-length: '.$info['size']);
header('Content-type: '.$info['type']);

if (url_segment(2) === 'download' || isset($_GET['download'])) {
	header('Content-Disposition: attachment; filename="'.$info['name'].'"');
}

return $info['content'];
