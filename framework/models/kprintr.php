<?php


if(load_library('ref', FALSE)) {

	function kprintr($variable, $return = FALSE) {
		if($return) {
			ob_start();
			r($variable);
			return ob_get_clean();
		}
		else {
			return r($variable);
		}
	}

} else {

	function kprintr($variable, $return = FALSE) {
		if($return) {
			ob_start();
			var_dump($variable);
			return ob_get_clean();
		}
		else {
			return var_dump($variable);
		}
	}

}