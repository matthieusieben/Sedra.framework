<?php

function cache_get($id) {
	if(config('cache.disabled', FALSE))
		return NULL;

	try {
		$query = db_select('cache','c')->fields('c', array('content'))->condition('id', $id);
		$result = $query->execute();
		$content = $result->fetchField();
		return $content ? unserialize($content) : NULL;
	} catch (PDOException $e) {
		return NULL;
	}
}

function cache_set($id, $content) {
	try {
		db_merge('cache')
			->key(array('id' => $id))
			->fields(array(
				'content' => serialize($content),
			))
			->execute();
	} catch (PDOException $e) {
		return NULL;
	}
}

function cache_delete($id = '%') {
	try {
		return db_delete('cache')
			->condition('id', $id, 'LIKE')
			->execute();
	} catch (PDOException $e) {
		return FALSE;
	}
}
