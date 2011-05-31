<?php

debug($args, '$args');

$data = array('title'=>'TITLE');

load::library('Krumo');

#Load::db();
#$r = db_query('SHOW TABLES');
#foreach($r as $a) debug($a);

#User::authenticate('admin','admin');
#User::logout();

$user = User::current();
debug($user->uid, 'User ID');

#$user->set_data(array('firstname'=>'Matthieu'));
#$user->set_data(array('lastname'=>'Sieben'));

##$user->set_data(array('firstname'=>NULL));
#$user->set_data(array('lastname'=>NULL));

debug($user->data('firstname'), 'firstname');
debug($user->data('lastname'), 'lastname');

return Load::view('index', $data);