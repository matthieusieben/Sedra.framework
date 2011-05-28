<?php

class Blocks {

	public static $blocks;

	public static function get($part = NULL, $data = array())
	{
		if(!isset(self::$blocks)) {
			# TODO: Set this up from DB
			self::$blocks = array(
				'header' => array(

				), # header
				'column' => array(

				), # column
				'footer' => array(

				), # foorer
			);
		}
		return akon(self::$blocks, $part, self::$blocks);
	}

}
