<?php

require_once 'includes/cache.php';

# Provides
global $schema;

function &schema_init() {
	global $schema;
	global $language;
	$cache_id = "schema/{$language}";
	if(!$schema && !($schema = cache_get($cache_id))) {
		$schema = array();
		foreach((array) explode(PATH_SEPARATOR, get_include_path()) as $module) {
			if(is_dir($module.'schemas')) {
				foreach((array) scandir($module.'schemas') as $file) {
					if($file && $file[0] !== '.') {
						$path = stream_resolve_include_path("schemas/{$file}");
						$table_info = require $path;
						$table_name = pathinfo($path, PATHINFO_FILENAME);

						sedra_schema_init($table_name, $table_info);

						$schema[$table_name] = $table_info;
					}
				}
			}
		}
		hook_invoke('schema', $schema);
		cache_set($cache_id, $schema);
	}
	return $schema;
}

function &schema_get_all() {
	return schema_init();
}

function &schema_get($table) {
	global $schema;
	schema_init();
	if(empty($schema[$table]['fields']))
		throw new FrameworkException(t('Loading schema for non existing table &laquo;@table&raquo;.', array('@table' => $table)), 404);
	return $schema[$table];
}

# Schema installation functions

function sedra_schema_init($table_name, &$table_info) {

	if(empty($table_info['name']))
		$table_info['name'] = $table_name;

	$table_info += array(
		'mysql_suffix' => 'DEFAULT CHARACTER SET UTF8',
		'mysql_engine' => 'InnoDB',
	);
}

function sedra_schema_install($table_info) {
	# Create table
	if(!empty($table_info['custom sql'])) {
		try {
			db_query($table_info['custom sql'])->execute();
		} catch (PDOException $e) {
			if(strpos($e->getMessage(), 'already exists') === FALSE)
				throw $e;
		}
	}
	else {
		foreach ($table_info['fields'] as $name => &$definition) {
			switch(strtolower(@$definition['type'])) {
			case 'enum':
				if(isset($definition['default']))
					# convert to string
					$definition['default'] = "{$definition['default']}";
				$definition['mysql_type'] = 'ENUM('.implode(',', array_map(function($f) {return "'".strtr($f,"'","\'")."'";}, array_keys($definition['options']))).')';
				break;
			case 'datetime':
				$definition['mysql_type'] = 'DATETIME';
				break;
			default:
				continue;
			}
		}

		db_create_table($table_info['name'], $table_info);

		# Create foreign key constraints
		if (!empty($table_info['foreign keys'])) {
			foreach ($table_info['foreign keys'] as $constraint_name => $constraint) {

				$table_name = $table_info['name'];
				$foreign_table = $constraint['table'];
				$action = @$constraint['cascade'] ? 'CASCADE' : 'SET NULL';
				$local_fields = '';
				$foreign_fields = '';

				foreach((array) @$constraint['columns'] as $local => $foreign) {
					$local_fields = ltrim($local_fields.','.$local,',');
					$foreign_fields = ltrim($foreign_fields.','.$foreign,',');
				}

				if($table_name && $foreign_table && $local_fields && $foreign_fields) {
					db_query("
						ALTER TABLE {{$table_name}}
						ADD CONSTRAINT {{$constraint_name}}
						FOREIGN KEY ( {$local_fields} ) REFERENCES {{$foreign_table}} ( {$foreign_fields} ) ON UPDATE {$action} ON DELETE {$action}
					");
				}
			}
		}
	}
}
