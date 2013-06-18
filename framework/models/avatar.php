<?php

function avatar_url( $account, $size = 256 ) {
	if(load_library('gravatar', FALSE)) {
		return gravatar_avatar( $account->mail, $size );
	}
	else {
		return NULL;
	}
}
