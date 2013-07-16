<?php

if(user_has_role(AUTHENTICATED_RID)) {
	redirect('account');
}
else if(user_login(NULL, val($arg[2]), val($arg[1]))) {
	redirect('account/credentials');
}
else {
	show_404();
}
