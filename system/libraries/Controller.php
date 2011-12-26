<?php

abstract class Controller {

	public $cache_flags;
	public $cache_hint;
	public $cache_key;
	public $allowed_methods = array('index');
	public $method;
	public $content;
	
	public function __construct() {
		# Get the method name
		$this->method = Url::segment(1, 'index');
		# Debug contoller
		debug($this, t('Main controller'));
	}

	public function method() {
		if(!isset($this->method)) {
			throw new SedraException(
				"Internal error",
				"Undefined controller method. Check your controller's constructor.");
		}

		if(!method_exists($this, $this->method)) {
			throw new Sedra404Exception();
		}

		if(!in_array($this->method, $this->allowed_methods)) {
			throw new SedraException(
				"Internal error",
				"Method not allowed. Check your controller's allowed methods.");
		}

		return $this->method;
	}

	public function get_cache_key()
	{
		if(isset($this->cache_key)) {
			return $this->cache_key;
		}

		if(!check_flags($this->cache_flags, CACHE_ENABLED))
		{
			return $this->cache_key = FALSE;
		}

		$id = array();

		$id['class'] = get_class($this);

		if( isset($this->id) )
		{
			$id['id'] = $this->id;
		}
		
		if(check_flags($this->cache_flags, CACHE_LEVEL_METHOD))
		{
			$id['method'] = $this->method;
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_URL))
		{
			$id['url'] = Url::current();
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_USER))
		{
			$user = User::current();
			
			$id['uid'] = $user->uid;
			
			if($user->uid === ANONYMOUS_UID)
			{
				$id['lang'] = $user->language;
			}
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_ROLE))
		{
			$user = User::current();
		
			$id['rid'] = $user->rid;
			$id['lang'] = $user->language;
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_LANG))
		{
			$user = User::current();
		
			$id['lang'] = $user->language;
		}

		return $this->cache_key = (array) $this->cache_hint + $id;
	}

	/************************************************************************/
	/*                    Static functions and variables                    */
	/************************************************************************/

	public static function generate(Controller $c) {		
		$cache_key = $c->get_cache_key();
		if(Cache::exists($cache_key)) {
			# If in cache, set content from cache
			$c->content = Cache::get($cache_key);
		} else {
			# Not in cache, generate and set cache
			try {
				$c->content = call_user_func(array($c, $c->method()));
			} catch (SedraException $e) {
				throw DEVEL ? $e : new Sedra404Exception();
			}
			$c = Hook::call(HOOK_GENERATE_CONTROLLER, $c);
			Cache::set($cache_key, $c->content);
		}
	}

	public static function render(Controller $c) {
		$string = Hook::call(HOOK_RENDER_CONTROLLER, $c->content);
		set_status_header(200);
		echo $string;
	}
}
