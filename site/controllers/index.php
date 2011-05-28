<?php

$data = array();

Load::db();
Database::startLog('default');
$r = db_query('SHOW TABLES');
foreach($r as $a) debug($a);

Render::site_view('index', $data);