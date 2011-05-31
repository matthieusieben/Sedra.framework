<?php

/**
 * This is an example controller class
 *
 * @author matthieusieben
 */
class Home extends Controller {
	
	public function __construct($args = NULL) {
		parent::__construct($args);
		# Library loading
		load::library('Krumo');
		
		$this->test();
	}


	public function index() {
		
		$user = User::current();
		
		$data = array(
			'title'=>t('Hello') . ' ' . $user->name,
		);
		
		$this->content = Load::view('home', $data);
	}
	
	public function test() {
		$user = User::current();
		debug($user->uid, 'User ID');

		#Load::db();
		
		#$r = db_query('SHOW TABLES');
		#foreach($r as $a) debug($a);
		
		#User::authenticate('admin','admin');
		#User::logout();
		
		#$user->set_data(array('firstname'=>'Matthieu'));
		#$user->set_data(array('lastname'=>'Sieben'));
		
		##$user->set_data(array('firstname'=>NULL));
		#$user->set_data(array('lastname'=>NULL));

		debug($user->data('firstname'), 'firstname');
		debug($user->data('lastname'), 'lastname');
	}

	public function in_cache() {
		return FALSE;
	}

	public function cache() {
		return FALSE;
	}
}

?>
