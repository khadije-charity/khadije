<?php
namespace content_protection\occasion\user;


class controller
{
	public static function routing()
	{


		$id = \dash\request::get('id');

		if(!$id || !\dash\coding::decode($id))
		{
			\dash\header::status(404, T_("Id not found"));
		}

		$result = \lib\app\occasion::get($id);

		if(!$result)
		{
			\dash\header::status(403, T_("Invalid occasion id"));
		}

		\dash\data::occasionDetail($result);
	}
}
?>