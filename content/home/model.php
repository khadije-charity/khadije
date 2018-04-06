<?php
namespace content\home;


class model extends \mvc\model
{
	public function post_donate()
	{
		$meta =
		[
			'turn_back'   => \dash\url::base(). '/doners',
			'other_field' =>
			[
				// 'hazinekard' => $way,
				// 'niyat'      => $niyat,
				'fullname'   => \lib\user::login('displayname'),
				'donate'     => 'cash',
				'doners'     => 0,
			]
		];

		\lib\utility\payment\pay::start(\lib\user::id(), 'asanpardakht', \dash\request::post('quickpay'), $meta);
	}

}
?>