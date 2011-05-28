<?php

class Site {
	public static function data($data = array())
	{
		Load::model('blocks');

		$data['lang'] = l();

		$data['meta']['author'] = 'Matthieu Sieben';
		$data['meta']['description'] = t('PHP Framework');
		$data['meta']['keywords'] = 'PHP, Framework';
		$data['meta']['copyright'] = 'Matthieu Sieben';
		$data['meta']['robots'] = 'index, follow';
		$data['meta']['revisit-after'] = '1 month';
		$data['meta']['expires'] = 'never';
		$data['meta']['abstract'] = $data['meta']['description'];
		$data['meta']['category'] = 'developpement';
		$data['meta']['language'] = $data['lang'];
		$data['meta']['audience'] = 'All';

		# $data['meta']['google-site-verification'] = NULL;

		$data['title'] = akon($data,'title','');
		$data['site_name'] = 'Sedra PHP Framework';
		$data['site_logo'] = Url::file('images/logo.png');
		$data['site_slogan'] = '';
		$data['copyright'] = 'Copyright &copy; 2008-'.date('Y ').$data['meta']['author'].'. All Rights Reserved.';

		$data['blocks'] = Blocks::get(NULL, $data);

		return $data;
	}
}