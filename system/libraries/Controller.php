<?php

abstract class Controller {
	
	public $content;
	
	public function __construct($args = NULL) {
		# Nothing todo at the moment
	}

	/**
	 * Default page method
	 */
	public abstract function index();

	/**
	 * Check if the page is in cache and loads it in $this->content.
	 */
	public abstract function in_cache();

	/**
	 * Cache the content of $this->content in the cache.
	 */
	public abstract function cache();

	/**
	 * Display the content of $this->content in the browser.
	 */
	public function render() {
		$string = Hook::call(HOOK_RENDER, $this->content);
		set_status_header(200);
		echo $string;
	}
}
