<?php

/**
 * Content management system class working along with the Sedra.framework
 */
class CMS {
	public static function setting($key) {
		switch ($key) {
		case 'theme':
			return 'default';
			break;
		
		default:
			return NULL;
			break;
		}
	}
}
