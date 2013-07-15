<?php

function cache_get($id) {
	try {
		$id = sha1(var_export($id, TRUE));
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
			->key(array('id' => sha1(var_export($id, TRUE))))
			->fields(array(
				'content' => serialize($content),
			))
			->execute();
	} catch (PDOException $e) {
		return NULL;
	}
}
