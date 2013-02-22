<?php

require_once 'user.php';

global $home_url;

if(user_has_role(AUTHENTICATED_RID)) {
	redirect($home_url);
}
else if(user_login(NULL, url_segment(2), url_segment(1))) {
	redirect($home_url);
}
else {
	show_404();
}
