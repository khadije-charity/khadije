<?php
namespace content\home;


class model
{
	public static function post()
	{
		if(\dash\request::post('salavat'))
		{
			$count =\lib\db\salavats::befrest();
			\dash\notif::result($count);
			return;
		}

		$meta =
		[
			'turn_back'   => \dash\url::base(). '/doners',
			'other_field' =>
			[
				// 'hazinekard' => $way,
				// 'niyat'      => $niyat,
				'fullname'   => \dash\user::login('displayname'),
				'donate'     => 'cash',
				'doners'     => 0,
			]
		];

		\dash\utility\payment\pay::start(\dash\user::id(), 'asanpardakht', \dash\request::post('quickpay'), $meta);
	}

}
?>