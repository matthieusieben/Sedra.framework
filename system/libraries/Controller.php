<?php

abstract class Controller {

	public $cache_flags	= DEFAULT_CACHE_FLAGS;
	public $cache_key	= array();

	public $method	= 'index';
	public $content	= NULL;

	/**
	 * This constructor builds a Controller object
	 *
	 * After this controller was called, "$this->method" should be set if the
	 * page is to be generated through the generic "_generate()" method. If you
	 * don't override this constructor, make sure that "$this->cache_flags" or
	 * "$this->cache_key" is properly set.
	 * 
	 */
	public function __construct($arg) {
		# Get the method name
		$this->method = Url::segment(1, 'index');
		# XXX : Enable hard caching by default. (TODO : make this optional?)
		$this->cache_flags |= CACHE_ENABLED;
	}

	public function _generate() {
		if(empty($this->method)) {
			throw new SedraException(
				"Internal error",
				"Undefined controller method. Check your controller's constructor.");
		}

		if($this->method[0] == '_') {
			# Not a public method
			throw new Sedra403Exception();
		}

		if(!is_callable($callback = array($this, $this->method))) {
			throw new Sedra404Exception();
		}

		return call_user_func($callback);
	}

	public function _get_cache_key() {
		static $already_computed = FALSE;
		if($already_computed) return $this->cache_key;
		$already_computed = TRUE;

		if(!check_flags($this->cache_flags, CACHE_ENABLED))
		{
			return $this->cache_key = FALSE;
		}

		$key = array();

		$key['class'] = get_class($this);

		if( isset($this->id) )
		{
			# item id
			$key['iid'] = $this->id;
		}
		
		if(check_flags($this->cache_flags, CACHE_LEVEL_METHOD))
		{
			$key['method'] = $this->method;
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_URL))
		{
			$key['url'] = Url::current();
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_ROLE))
		{
			$user = User::current();
		
			$key['rid'] = $user->rid;
			$key['language'] = $user->language;
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_LANG))
		{
			$user = User::current();

			$key['language'] = $user->language;
		}

		if(check_flags($this->cache_flags, CACHE_LEVEL_USER))
		{
			$user = User::current();
			
			$key['uid'] = $user->uid;
			
			if($user->uid === ANONYMOUS_UID) {
				$key['language'] = $user->language;
			}
			else {
				# A user may only diplay the website in one language
				unset($key['language']);
			}
		}

		return $this->cache_key += $key;
	}

	/************************************************************************/
	/*                    Static functions and variables                    */
	/************************************************************************/

	public static function generate(Controller $c) {		
		$cache_key = $c->_get_cache_key();
		if(Cache::exists($cache_key)) {
			# If in cache, set content from cache
			$c->content = Cache::get($cache_key);
		} else {
			# Not in cache, generate and set cache
			try {
				$c->content = $c->_generate();
			} catch (SedraException $e) {
				throw DEVEL ? $e : new Sedra404Exception();
			}
			$c = Hook::call(HOOK_GENERATE_CONTROLLER, $c);
			Cache::set($cache_key, $c->content);
		}
	}

	public static function render(Controller $c) {
		$c = Hook::call(HOOK_RENDER_CONTROLLER, $c);
		set_status_header(200);
		echo $c->content;
	}
}
