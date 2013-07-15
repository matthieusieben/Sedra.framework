<?php

require_once 'includes/database.php';
require_once 'includes/user.php';

function file_was_sent($field_name) {
	return !empty($_FILES[$field_name]) && file_exists($_FILES[$field_name]['tmp_name']);
}

function file_upload($field_name, $old_fid = NULL) {
	global $user;

	if (file_was_sent($field_name)) {
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

		# Try to update the file $old_fid if it exists.
		if($old_fid && file_update($old_fid, $info))
			return file_info(array('fid' => $old_fid));

		# Otherwise create a new file
		do {
			try {
				$info['hash'] = random_salt();
				$fid = db_insert('files')->fields($info)->execute();
				return file_info(array('fid' => $fid));
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

function file_info($fid) {

	static $gc = FALSE;
	if(!$gc) {
		# TODO : do this during cron task
		file_garbage_collector();
		$gc = TRUE;
	}

	if(!is_array($fid))
		return NULL;

	list($key, $value) = each($fid);

	if(!$key || !$value)
		return NULL;

	$info = db_select('files', 'f')
		->fields('f')
		->condition($key, $value)
		->execute()
		->fetchAssoc();

	if ($info) {
		$info['url'] = array(
			'path' => 'file/'.$info['hash'],
			'title' => $info['name'],
		);
		url_setup($info['url']);
	}

	return $info;
}

function file_can_be_editted($fid) {
	global $user;

	$info = file_info(array('fid' => $fid));
	if(!$info) return FALSE;

	if (user_has_role(MODERATOR_RID))
		return TRUE;

	return (int) $info['uid'] === (int) $user->uid;
}

function file_delete($fid) {
	if (file_can_be_editted($fid)) {
		db_delete('files')->condition('fid', $fid)->execute();
	}
}

function file_update($fid, array $info) {
	# Nothing to update
	if (!$fid) return FALSE;

	if (file_can_be_editted($fid)) {
		$r = db_update('files')
			->fields($info)
			->condition('fid', $fid)
			->execute();
		return $r === 1;
	}
	return FALSE;
}

function file_save($fid) {
	return file_update($fid, array('tmp' => 0));
}

function file_garbage_collector() {
	# Delete files posted more than one day ago but not saved.
	db_delete('files')
		->condition('tmp', 1)
		->condition('posted', REQUEST_TIME - 86400, '<')
		->execute();
}
