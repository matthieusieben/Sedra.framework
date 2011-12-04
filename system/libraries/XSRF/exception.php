<?php

class SedraXSRFException
extends SedraException
{
	public function __construct()
	{
		parent::__construct( 'Cross Site Request Forgery Protection', 'XSRF detected, validation failure.', array(), 203 );
	}
}
