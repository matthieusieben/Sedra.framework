<?php

function blocks_html_head() {
	if(count(Blocks::get())) {
		Load::helper('html');
		css(stream_resolve_include_path('blocks.css'));
	}
}

function blocks_alter_view_data($data) {
	$column_blocks = Blocks::get('column');
	if(!empty($column_blocks)) {
		$data['body']['class'][] = 'two_columns';
	}
	return $data;
}
