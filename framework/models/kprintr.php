<?php

require_once FRAMEWORK_ROOT.'libraries/ref/bridge.php';

function kprintr($variable, $return = FALSE) {
	if($return) {
		return @rt($variable);
	}
	else {
		return r($variable);
	}
}
