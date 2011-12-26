<?php

abstract class Controller {

	public $cache_flags;
	public $cache_key;
	public $method;
	public $content;
	
	public function __construct() {
		# Get the method name
		$this->method = Url::segment(1, 'index');
		# Debug contoller
		debug($this, t('Main controller'));
	}

	public static function generate(Controller $c) {		
		if(Cache::exists($c)) {
			$c->content = Cache::get($c);
		} else {
			if(!isset($c->method)) {
				throw new SedraException("Programation error","Undefined controller method. Check your controller's constructor.");
			}
			elseif(method_exists($c, $c->method)) {
				try {
					# No way to prevent calling static functions except if they need arguments
					$c->content = call_user_func(array($c, $c->method));
				} catch (SedraErrorException $e) {
					if(DEVEL) {
						throw $e;
					} else {
						throw new Sedra404Exception();
					}
				}
				$c = Hook::call(HOOK_GENERATE_CONTROLLER, $c);
				Cache::set($c, $c->content);
			} else {
				throw new Sedra404Exception();
			}
		}
	}

	public static function render(Controller $c) {
		$string = Hook::call(HOOK_RENDER_CONTROLLER, $c->content);
		set_status_header(200);
		echo $string;
	}
}
