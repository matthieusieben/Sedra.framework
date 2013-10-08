<?php

if(user_has_role(AUTHENTICATED_RID)) {
	redirect('account');
}
else if(user_login(NULL, $args['key'], $args['action'])) {
	redirect('account/credentials');
}
else {
	show_404();
}
