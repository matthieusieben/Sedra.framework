<?php

/**
 * Controller used to display exceptions
 */
class ExceptionController extends Controller {

	public $cache_flags	= 0;

	protected $exception;
	protected $full_page;
	
	public function __construct($arg) {
		$this->exception = $arg['exception'];
		$this->full_page = val($arg['full-page'], TRUE);
	}

	public function _generate() {
		$code = 500;
		$heading = t('Runtime Error');
		$view = 'exception';

		if($this->exception instanceof SedraException) {
			$code = $this->exception->getCode();
			$heading = $this->exception->getHeading();
		}

		$data = array(
			'code' => $code,
			'title' => $heading,
			'e' => $this->exception,
		);
		
		if($this->full_page) {
			$data['previous_output'] = close_buffers();
			$view = 'exception_page';
		}

		return $this->content = Theme::view($view, $data);
	}
}

