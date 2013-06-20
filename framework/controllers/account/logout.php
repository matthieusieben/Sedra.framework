<?php

load_model('user');

user_logout();

redirect(config('site.home', 'index'));
