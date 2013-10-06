<?php

require_once 'includes/file.php';

if (headers_sent()) {
	throw new FrameworkException(t('HTTP Headers already sent while trying to output a file.'));
}

$info = file_info(array('hash' => val($arg[0])));

if (!$info) show_404();

hook_invoke('file display', $info);

set_status_header(200);

header('Content-length: '.$info['size']);
header('Content-type: '.$info['type']);

if (val($arg[1]) === 'download' || isset($_GET['download'])) {
	header('Content-Disposition: attachment; filename="'.$info['name'].'"');
}

return $info['content'];
