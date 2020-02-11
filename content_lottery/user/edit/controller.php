<?php
namespace content_lottery\user\edit;

class controller
{

	public static function routing()
	{

		$nationalcode = \dash\request::get('nationalcode');
		if($nationalcode && \dash\utility\filter::nationalcode($nationalcode))
		{

			$load_detail = \lib\db\karbala2users::get(['nationalcode' => $nationalcode, 'limit' => 1]);
			if(!$load_detail)
			{
				\dash\header::status(403);
			}

			\dash\data::myUser($load_detail);

		}
		else
		{
			\dash\header::status(404);
		}


	}
}
?>