<?php

switch (Url::segment(1)) {
	case '403':
		throw new Sedra403Exception();
		break;

	case '404':
	default:
		throw new Sedra404Exception();
		break;
}
