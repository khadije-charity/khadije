<?php
namespace content_api\v6\smsapp;


class controller
{
	public static function routing()
	{
		self::check_smsappkey();

		if(\dash\url::directory() === 'v6/smsapp/new' && \dash\request::is('post'))
		{
			$detail = self::add_new_sms();
			\content_api\v6::bye($detail);
		}
		else
		{
			\content_api\v6::no(404);
		}

	}


	private static function check_smsappkey()
	{
		$smsappkey = \dash\header::get('smsappkey');

		if(!trim($smsappkey))
		{
			\content_api\v6::no(400, T_("Appkey not set"));
		}

		if($smsappkey === '1233')
		{
			return true;
		}
		else
		{
			\content_api\v6::no(400, T_("Invalid app key"));
		}

	}


	private static function check_allow_gateway($_mobile)
	{
		$_mobile = \dash\utility\filter::mobile($_mobile);

		$check_allow_gateway =
		[
			'989109610612', // reza
			'989357269759', // javad
			'sobati', // sobati need to get mobile
			'khalili', // khalili need to get mobile
		];

		if(in_array($_mobile, $check_allow_gateway))
		{
			return true;
		}
		else
		{
			\content_api\v6::no(400, T_("Invalid mobile for gateway"));
		}
	}


	private static function add_new_sms()
	{
		// check gateway to not run this application in other device
		$gateway = \dash\request::post('gateway');
		self::check_allow_gateway($gateway);


		// check from is not block or family
		$from        = \dash\request::post('from');
		if($from && mb_strlen($from) > 90)
		{
			\dash\notif::error(T_("Invalid from"));
			return false;
		}

		$text        = \dash\request::post('text');

		$date        = \dash\request::post('date');

		if($date && !strtotime($date))
		{
			\dash\notif::error(T_("Invalid date"));
			return false;
		}


		$insert                   = [];
		$insert['fromnumber']     = $from;
		$insert['togateway']      = $gateway;
		$insert['fromgateway']    = null;
		$insert['tonumber']       = null;
		$insert['user_id']        = null;
		$insert['date']           = date("Y-m-d H:i:s", strtotime($date));
		$insert['text']           = $text;
		$insert['uniquecode']     = null;
		$insert['reseivestatus']  = 'awaiting';
		$insert['sendstatus']     = null;
		$insert['amount']         = null;
		$insert['answertext']     = null;
		$insert['group_id'] = null;
		$insert['recomand_id']    = null;

		self::check_need_analyze($insert);

		$id = \lib\db\sms::insert($insert);

		if($insert['group_id'])
		{
			\lib\db\smsgroup::update_group_count($insert['group_id']);
		}

		if($id)
		{
			return ['smsid' => \dash\coding::encode($id)];
		}
		return false;
	}


	private static function check_need_analyze(&$insert)
	{
		$number       = $insert['fromnumber'];
		$mobileNumber = \dash\utility\filter::mobile($number);

		if($mobileNumber)
		{
			$get = \lib\db\smsgroupfilter::get(['1.1' => [" = 1.1" , "AND ( `number` = '$number' OR `number` = '$mobileNumber') "], 'limit' => 1]);
		}
		else
		{
			$get = \lib\db\smsgroupfilter::get(['number' => $number, 'limit' => 1]);
		}


		// this number not found in any filter
		if(!isset($get['group_id']))
		{
			return;
		}

		$insert['group_id'] = $get['group_id'];

		$get_group = \lib\db\smsgroup::get(['id' => $get['group_id'], 'limit' => 1]);

		if(isset($get_group['status']) && $get_group['status'] === 'enable')
		{
			if(array_key_exists('analyze', $get_group) && !$get_group['analyze'])
			{
				$insert['reseivestatus']  = 'block';
			}
		}

	}
}
?>