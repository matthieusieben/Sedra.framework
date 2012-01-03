<?php

/**
* 
*/
class TextBlock extends Controller
{
	function __construct($arg)
	{
		parent::__construct($arg);
		if($this->is_main) {
			throw new Sedra404Exception();
		}
	}

	function block() {
		if(empty($this->title) && empty($this->text)) {
			return;
		}
		$data = array(
			'title' => val($this->title),
			'text' => val($this->text),
		);
		return Theme::view('block', $data);
	}
}

