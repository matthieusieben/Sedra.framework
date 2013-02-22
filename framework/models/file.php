<?php

require_once 'database.php';

file_garbage_collector();

function file_upload($field_name) {
	global $user;

	if (!empty($_FILES[$field_name]) && file_exists($_FILES[$field_name]['tmp_name'])) {
		$_FILE = $_FILES[$field_name];

		$fp = fopen($_FILE['tmp_name'], 'r');
		$content = fread($fp, filesize($_FILE['tmp_name']));
		fclose($fp);

		$info = array(
			'name' => $_FILE['name'],
			'size' => $_FILE['size'],
			'type' => $_FILE['type'],
			'posted' => REQUEST_TIME,
			'content' => $content,
			'uid' => $user->uid,
			'tmp' => 1,
		);

		do {
			try {
				$info['hash'] = random_salt();
				$fid = db_insert('files')->fields($info)->execute();
				return file_info($fid);
			} catch(PDOException $e) {
				if (strpos($e->getMessage(), '1062') !== FALSE) {
					# Hash already in use
					continue;
				}
				else {
					throw $e;
				}
			}
		} while(TRUE);
	}

	return NULL;
}

function file_info($fid, $field = NULL) {
	$query = db_select('files', 'f')
		->fields('f')
		->condition(strlen($fid) === 32 ? 'hash' : 'fid', $fid);

	$info = $query->execute()->fetchAssoc();

	if ($info) {
		$info['url'] = array(
			'path' => 'file/'.$info['hash'],
			'title' => $info['name'],
		);
		url_setup($info['url']);
	}

	return is_null($field) ? $info : $info[$field];
}

function file_can_be_editted($fid) {
	global $user;
	return user_has_role(MODERATOR_RID) || ((int) file_info($fid, 'uid') === (int) $user->uid);
}

function file_delete($fid) {
	if (file_can_be_editted($fid)) {
		db_delete('files')->condition('fid', $fid)->execute();
	}
}

function file_save($fid) {
	# Nothing to save
	if (!$fid) return TRUE;

	if (file_can_be_editted($fid)) {
		$r = db_update('files')
			->fields(array(
				'tmp' => 0,
			))
			->condition('fid', $fid)
			->execute();
		return $r === 1;
	}
	return FALSE;
}

function file_garbage_collector() {
	# Delete files posted more than one day ago but not saved.
	db_delete('files')
		->condition('tmp', 1)
		->condition('posted', REQUEST_TIME - 86400, '<')
		->execute();
}
