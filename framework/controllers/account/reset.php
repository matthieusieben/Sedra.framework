<?php

require_once 'user.php';

if(user_has_role(AUTHENTICATED_RID)) {
	redirect('account');
}
else if(user_login(NULL, url_segment(2), url_segment(1))) {
	redirect('account/password');
}
else {
	show_404();
}
