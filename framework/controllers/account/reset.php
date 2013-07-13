<?php

load_model('user');

if(user_has_role(AUTHENTICATED_RID)) {
	redirect('account');
}
else if(user_login(NULL, url_segment(2), url_segment(1))) {
	redirect('account/credentials');
}
else {
	show_404();
}
