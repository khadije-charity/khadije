<?php
namespace content\donate;

class model
{
	public static function post()
	{
		$args =
		[
			'username' => \dash\request::post('username'),
			'bank'     => mb_strtolower(\dash\request::post('bank')),
			'niyat'    => \dash\request::post('niyat'),
			'way'      => \dash\request::post('way'),
			'fullname' => \dash\request::post('fullname'),
			'email'    => \dash\request::post('email'),
			'mobile'   => \dash\request::post('mobile'),
			'amount'   => intval(\dash\request::post('amount')) / 10,
			'doners'   => \dash\request::post('doners') === 'yes' ? 1 : 0,
		];

		if(!in_array(mb_strtolower(\dash\request::post('bank')), ['asanpardakht', 'zarinpal', 'payir']))
		{
			\dash\notif::error(T_("Bank id is not correct"));
			return false;
		}


		$redirect = false;
		if(\dash\permission::check('donateManualPay'))
		{
			$redirect = true;
			$args['manuall'] = \dash\request::post('manualPay');
		}

		\lib\app\donate::add($args);

		if($redirect)
		{
			\dash\redirect::pwd();
		}
	}
}
?>