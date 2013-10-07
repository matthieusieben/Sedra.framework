<?php

if(user_has_role(AUTHENTICATED_RID)) {
	redirect('account');
}
else if(user_login(NULL, val($arg[1]), val($arg[0]))) {
	redirect('account/credentials');
}
else {
	show_404();
}
