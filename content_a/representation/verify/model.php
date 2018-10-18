<?php
namespace content_a\representation\verify;


class model
{

	public static function post()
	{

		$post            = [];
		$post['need_id'] = \dash\request::get('id');
		$post['status']  = 'draft';
		$post['type']    = 'representation';

		$service_id = \lib\app\service::add($post);

		if(\dash\engine\process::status() && $service_id)
		{
			\dash\notif::ok(T_("Your representation was saved"));
			\dash\redirect::to(\dash\url::here(). '/representation/profile?id='. $service_id);
		}
	}
}
?>
