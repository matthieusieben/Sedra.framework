<?php

Load::user();

define('USER_CONTROLLER_CACHE_LEVEL', CACHE_LEVEL_USER | CACHE_LEVEL_METHOD);

/**
 * @author matthieusieben
 */
class Profile extends Controller {

	# Each user has a different Profile page.
	public $cache_flags	= USER_CONTROLLER_CACHE_LEVEL;

	public function index() {
		if(!User::connected()) {
			redirect('Profile/login');
		}
		
		$data = array(
			'title' => t('User profile'),
			'user' => User::current(),
		);
		
		return Load::view('profile/index', $data);
	}

	public function login()
	{
		$data = array(
			'title' => t('Login page'),
			'action' => Url::make(Input::get('redirect', 'Profile'), $_GET),
		);
		return Load::view('profile/login', $data);
	}
}

?>
