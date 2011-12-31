<?php

/**
 * This is the default home controller displaying a welcome message.
 *
 * @author matthieusieben
 */
class Home extends Controller {

	# Each user has a different home page
	public $cache_flags	= CACHE_LEVEL_USER;

	/**
	 * Home/index.html page.
	 *
	 * @return string the HTML content of the home page
	 */
	public function index() {
		$user = User::current();

		$data = array(
			'title' => t(
				'Welcome, !user!',
				array('!user' => t($user->data('firstname', $user->name)))),
		);

		return Theme::view('home', $data);
	}
}
