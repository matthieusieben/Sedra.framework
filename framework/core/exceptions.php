<?php

class FrameworkException extends Exception {
	public $code = 500;

	public function __construct($message, $code = 500) {

		if($message instanceof Exception)
			$message = $message->message;

		parent::__construct($message, $code);
	}

	public function setCode($code) {
		if(is_null($code))
			return $this->code;
		else
			return $this->code = $code;
	}
}

class FrameworkLoadException extends FrameworkException {
	public function __construct($message) {
		parent::__construct($message, 500);
	}
}

class FrameworkSettingsException extends FrameworkException {
	public function __construct() {
		parent::__construct('Could not load the settings file.', 500);
	}
}
