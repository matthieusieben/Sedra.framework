<?php

/**
 * This is the default controller
 *
 * @author matthieusieben
 */
class Home extends Controller {

	public $cache_flags = CACHE_LEVEL_LANG;

	public function index() {
		$user = User::current();
		
		$data = array(
			'title' => t('Welcome') . ', ' . $user->data('firstname', $user->name) . '!',
		);
		
		return Load::view('home', $data);
	}
}

?>
