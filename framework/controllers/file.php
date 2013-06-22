<?php

load_model('file');

if (headers_sent()) {
	throw new FrameworkException(t('HTTP Headers already sent while trying to output a file.'));
}

$info = file_info(array('hash' => url_segment(1)));
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
