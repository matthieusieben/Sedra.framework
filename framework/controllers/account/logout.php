<?php

require_once 'includes/user.php';

user_logout();

redirect(config('site.home', 'index'));
