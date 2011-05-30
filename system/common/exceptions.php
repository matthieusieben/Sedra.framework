<?php

class SedraException
extends Exception
{
	public $heading;

	/**
	 * Set exception message.
	 *
	 * @param	string	The error message
	 * @param	mixed	Additionnal parameters for message translation
	 */
	public function __construct($heading, $message, $args = array(), $code = 500)
	{
		parent::__construct( t($message, $args), $code );
		$this->heading = t($heading, $args);
		$this->code = $code;
	}

	public function getHeading()
	{
		return $this->heading;
	}
}

class Sedra403Exception
extends SedraException
{
	public function __construct()
	{
		parent::__construct(
			'Access Forbidden',
			'You do not have the permission to view this page.',
			array(),
			403);
	}
}

class Sedra404Exception
extends SedraException
{
	public function __construct()
	{
		parent::__construct(
			'Page not found',
			'The URL you requested @url was not found on this server.',
			array(
				'@url' => Url::$query_string,
			),
			404);
	}
}

class LoadException
extends SedraException
{
	public function __construct( $what, $name )
	{
		parent::__construct(
			'A file could not be loaded',
			'Could not load the @kind "@name".',
			array(
				'@name' =>		$name,
				'@kind' =>		t($what)));
	}
}