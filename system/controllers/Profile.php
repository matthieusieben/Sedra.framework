<?php

Load::user();

/**
 * @author matthieusieben
 */
class Profile extends Controller {

	public $allowed_methods = array('index', 'login');
	public $cache_flags = CACHE_LEVEL_USER;
	public $cache_hint = array('band' => 'the_rockers');

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
