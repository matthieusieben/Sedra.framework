<?php

require_once 'user.php';

user_logout();

redirect(config('site.home', 'index'));
