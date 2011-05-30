<?php

class Render {

	public static function view($view, $__data = array())
	{
		echo Load::view($view, $__data);
	}

	public static function exception($e)
	{
		set_status_header($e instanceof SedraException ? $e->getCode() : 500);
		Render::view('exception', array('e' => $e));
	}
}
