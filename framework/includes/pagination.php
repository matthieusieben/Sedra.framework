<?php

/* type = full | minimal | normal */
function pagination($count, $at, $url_schema, $ipp = 20, $type = 'full') {
	$count = (int) $count;
	$at = (int) $at;
	$ipp = (int) $ipp;

	if($count <= $ipp || $at >= $count || $ipp === 0)
		return NULL;

	$at_page = (int) ($at / $ipp);
	$last_page = (int) ($count / $ipp) - ($count % $ipp ? 0 : 1);

	$pagination = array(
		'type' => $type,
		'count' => $count,
		'at' => $at,
		'ipp' => $ipp,
		'at_page' => $at_page,
		'last_page' => $last_page,
		'view' => 'components/pagination',
	);

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

	if($type !== 'minimal') {
		$pagination['items'] = array();

		$start = 0;
		$end = $last_page;

		if($type !== 'full') {
			$start = max($start, $at_page - 1);
			$end = min($end, $at_page + 1);
		}

		for($i = $start; $i <= $end; $i++) {
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
	}

	$pagination['next'] = array(
		'path' => strtr($url_schema, array(
			'!page' => min($last_page, $at_page + 1),
			'!limit' => $ipp,
			'!start' => min($count - 1, ($at_page + 1) * $ipp),
		)),
		'attributes' => array(
			'class' => array(
				$at_page === $last_page ? 'disabled' : '',
			),
		),
	);

	return $pagination;
}