<?php

class Blocks {
	public static function get($position = NULL)
	{
		$test_data = array(
			'title'=>'Yo man',
			'text' => 'lorem ipsum',
			'method' => 'block',
			'controller' => 'textblock',
		);

		$blocks = array(
			'column' => array(
			),
			'footer' => array(
				$test_data + array('id' => 1),
				$test_data + array('id' => 2),
				$test_data + array('id' => 3),
			),
		);
		
		return $position ? val($blocks[$position]) : $blocks;
	}

	public static function generate($block_data)
	{
		if(empty($block_data['controller'])) {
			return;
		}

		return '<?php
		$arg = '.var_export($block_data, TRUE).';
		$c = Load::controller($arg["controller"], $arg);
		Controller::toBrowser($c);
		?>';
	}
}