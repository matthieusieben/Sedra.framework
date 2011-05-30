<?php

$data = array('title'=>'TITLE');

#Load::db();
#$r = db_query('SHOW TABLES');
#foreach($r as $a) debug($a);

// TODO : REMOVE THESE LINES
#User::authenticate('admin','admin');
#User::logout();
// ENDTODO

#$user = User::current();
#$user->set_data(array('firstname'=>'Matthieu Sieben'));
#$user->set_data(array('firstname'=>NULL));
#debug($user->data('firstname'), 'firstname');
#debug($user->uid, 'User ID');

Render::view('index', $data);