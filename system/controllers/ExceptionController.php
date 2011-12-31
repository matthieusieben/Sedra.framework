<?php

/**
 * Controller used to display exceptions
 */
class ExceptionController extends Controller {

	public $cache_flags	= 0;

	protected $exception;

	public function page() {
		$data = $this->_data();
		$data['previous_output'] = close_buffers();
		return Theme::view('exception_page', $data);
	}

	public function block() {
		$data = $this->_data();
		return Theme::view('exception', $data);
	}

	private function _data() {
		if(!isset($this->exception) OR !($this->exception instanceof Exception)) {
			$d = debug_backtrace();
			$caller = @$d[1];
			throw new SedraPHPErrorException(
				'Invalid use of the ExceptionController.',
				E_WARNING, @$caller['file'], @$caller['line']);
		}
		
		$data = array('e' => $this->exception);

		if($this->exception instanceof SedraException) {
			$data['code'] = $this->exception->getCode();
			$data['title'] = $this->exception->getHeading();
		} else {
			$data['code'] = 500;
			$data['title'] = t('Runtime Error');
		}

		return $data;
	}
}

