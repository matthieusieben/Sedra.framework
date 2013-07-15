<?php

function pagination($count, $at, $url_schema, $ipp = 20) {
	$count = (int) $count;
	$at = (int) $at;
	$ipp = (int) $ipp;

	if($count <= $ipp || $at >= $count || $ipp === 0)
		return NULL;

	$at_page = (int) ($at / $ipp);

	$pagination = array();

	$pagination['prev'] = array(
		'path' => strtr($url_schema, array(
			'!page' => max(0, $at_page - 1),
			'!limit' => $ipp,
			'!start' => max(0, ($at_page - 1) * $ipp),
		)),
		'attributes' => array(
			'class' => array(
				$at_page == 0 ? 'disabled' : '',
			),
		),
	);

	$pagination['items'] = array();
	for($i = 0; $i < $count / $ipp; $i++) {
		$pagination['items'][$i] = array(
			'path' => strtr($url_schema, array(
				'!page' => $i,
				'!limit' => $ipp,
				'!start' => $i * $ipp,
			)),
			'title' => $i,
			'attributes' => array(
				'class' => array(
					$i === $at_page ? 'active' : '',
				),
			),
		);
	}

	$pagination['next'] = array(
		'path' => strtr($url_schema, array(
			'!page' => min($count / $ipp, $at_page + 1),
			'!limit' => $ipp,
			'!start' => min($count - 1, ($at_page + 1) * $ipp),
		)),
		'attributes' => array(
			'class' => array(
				$at_page === (int) ($count / $ipp) ? 'disabled' : '',
			),
		),
	);

	return $pagination;
}