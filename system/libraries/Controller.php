<?php

abstract class Controller {

	public $cache_flags	= CACHE_DEFAULT_FLAGS;
	public $cache_key	= array();
	public $cache		= NULL; # Cache data

	public $method	= 'index';
	public $content	= NULL;
	public $html	= NULL;

	public $is_main = FALSE;

	/**
	 * This constructor builds a Controller object
	 *
	 * After the constructor was called, "$this->method" should be set if the
	 * page is to be generated through the generic "_generate()" method. If you
	 * don't override this constructor, make sure that "public $cache_flags" or
	 * "public $cache_key" are correctly defined.
	 * 
	 */
	public function __construct($arg = array()) {
		# Set attributes from the argument array
		foreach((array) $arg as $k => $v) {
			$this->$k = $v;
		}
	}

	/**
	 * Calls the method whose name is contained in $this->method in order to
	 * generate this controller's content. The return value of this method will
	 * be stored in $this->content and returned. If $this->method is not set,
	 * starts with an underscore or is not callable on $this, an exception will
	 * be thrown.
	 *
	 * @return string The content generated by the appropriate method
	 * @throws SedraException if $this->method is not defined
	 * @throws Sedra403Exception if $this->method starts with '_'
	 * @throws Sedra404Exception if the method is not callable
	 */
	public function _generate() {
		if(empty($this->method)) {
			throw new SedraException(
				"Internal error",
				"Undefined controller method. Check your controller's constructor.");
		}
		
		# Not a public method
		if($this->method[0] == '_') {
			throw new Sedra403Exception();
		}

		# Not a valid method
		if(!is_callable($callback = array($this, $this->method))) {
			throw new Sedra404Exception();
		}

		# Call the method
		return $this->content = call_user_func($callback);
	}

	/**
	 * Evaluates $this->content and stores the result in $this->html
	 *
	 * @return void
	 */
	public function _render() {
		ob_start();
		
		eval('?>'.$this->content);
		
		$this->html = ob_get_clean();
	}

	/**
	 * Prints the result of this controller.
	 *
	 * @return void
	 */
	public function _display() {
		echo $this->html;
	}

	/**
	 * Get the key to store/retrieve this controller's $content from the cache.
	 * 
	 * This method automatically computes the key from the flags. If you decide
	 * to implement your own method, make sure that the return array at least
	 * containing a 'class' key with your class name to avoid conflicts with
	 * other controller. This behavior can be obtained by setting $cache_flags
	 * to CACHE_ENABLED while $cache_key is an empty array.
	 * 
	 * Note: The set of keys must be unique for each item you wish to store
	 * in the cache.
	 * Note: If $this->cache_flags contains keys before this method is called, these
	 * keys won't be modified.
	 * 
	 * @return array The set of keys identifying the current instance of the controller
	 */
	public function _get_cache_key() {
		if(!check_flags($this->cache_flags, CACHE_ENABLED))
		{
			return FALSE;
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
		}

		return $this->cache_key += $key;
	}

	/************************************************************************/
	/*                    Static functions and variables                    */
	/************************************************************************/

	/**
	 * Setup the content of a controller from the cache if it exists or from
	 * is _generate() method.
	 * 
	 * @param Controller $c The controller to generate
	 * @return void
	 */
	public static function generate(Controller $c) {	
		if(!isset($c->content)) {	
			$cache_key = $c->_get_cache_key();
			if($cache = Cache::get($cache_key)) {
				# If in cache, set content from cache
				$c->cache =& $cache;
				$c->content =& $cache->data;
			} else {
				# Not in cache, generate and set cache
				$c = Hook::alter('set_controller_content', $c);
				# Hooks could set the content
				if(!isset($c->content)) {
					try {
						$c->_generate();
					} catch (SedraException $e) {
						throw DEVEL ? $e : new Sedra404Exception();
					}
				}
				Cache::set($cache_key, $c->content);
			}
		}
	}

	/**
	 * This function renders the controller by evaluating the php code contained
	 * in its $content variable and storing the result in $hmtl.
	 *
	 * @param Controller $c 
	 * @return void
	 */
	public static function render(Controller $c) {
		$c = Hook::alter('set_controller_html', $c);
		if(!isset($c->html)) {
			try {
				$c->_render();
			} catch (SedraException $e) {
				throw DEVEL ? $e : new Sedra404Exception();
			}
		}
	}

	/**
	 * Prints the controller's $html attribute.
	 *
	 * @param Controller $c 
	 * @return void
	 */
	public static function display(Controller $c) {
		$c = Hook::alter('alter_controller_before_display', $c);
		try {
			$c->_display();
		} catch (SedraException $e) {
			throw DEVEL ? $e : new Sedra404Exception();
		}
	}
	
	public static function toBrowser(Controller $c) {
		self::generate($c);
		self::render($c);
		self::display($c);
	}
}
