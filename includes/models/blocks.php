<?php

class Blocks {

	public static $blocks;

	public static function get($part = NULL, $data = array())
	{
		if(!isset(self::$blocks)) {
			# LATER: Set this up from DB
			self::$blocks = array(
				'header' => array(

				), # header
				'column' => array(
					Load::view('user', $data),
				), # column
				'footer' => array(

				), # foorer
			);
		}
		return akon(self::$blocks, $part, self::$blocks);
	}

}
