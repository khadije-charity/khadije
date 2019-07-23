<?php
namespace content_api\v6\smsapp;


class newsms
{
	private static $update_insert = 'insert';
	private static $sms_id        = null;


	public static function add_new_sms()
	{

		// check from is not block or family
		$from        = \dash\request::post('from');
		if($from && mb_strlen($from) > 90)
		{
			\dash\notif::error(T_("Invalid from"));
			\dash\log::set('apiSmsAppInvalidFrom');
			return false;
		}

		$text        = \dash\request::post('text');

		$date        = \dash\request::post('date');

		if($date && !strtotime($date))
		{
			\dash\notif::error(T_("Invalid date"));
			\dash\log::set('apiSmsAppInvalidDate');
			return false;
		}


		$from_mobile = \dash\utility\filter::mobile($from);
		$user_id     = null;

		// if from is mobile signup it
		if($from_mobile)
		{
			$from        = $from_mobile;
			$get_user_id = \dash\db\users::get_by_mobile($from_mobile);

			if(isset($get_user_id['id']))
			{
				$user_id = $get_user_id['id'];
			}
			else
			{
				$user_id = \dash\db\users::signup(['mobile' => $from_mobile]);
			}
		}

		if(!$from)
		{
			\dash\notif::error(T_("From number is required"));
			\dash\log::set('apiSmsAppFromIsNull');
			return false;
		}

		$brand         = \dash\request::post('brand');
		$model         = \dash\request::post('model');
		$simcartserial = \dash\request::post('simcart-serial');
		$smsmessageid  = \dash\request::post('smsMessage-id');
		$userdata      = \dash\request::post('userdata');


		$insert                  = [];
		$insert['brand']         = substr($brand, 0, 99);
		$insert['model']         = substr($model, 0, 99);
		$insert['simcartserial'] = substr($simcartserial, 0, 99);
		$insert['smsmessageid']  = substr($smsmessageid, 0, 99);
		$insert['userdata']      = substr($userdata, 0, 99);
		$insert['fromnumber']    = $from;
		$insert['togateway']     = \dash\utility\filter::mobile(\dash\header::get('gateway'));
		$insert['fromgateway']   = null;
		$insert['tonumber']      = null;
		$insert['user_id']       = $user_id;
		$insert['datereceive']   = date("Y-m-d H:i:s");
		$insert['date']          = date("Y-m-d H:i:s", strtotime($date));
		$insert['text']          = $text;
		$insert['smscount']      = mb_strlen($text);
		$insert['uniquecode']    = null;
		$insert['receivestatus'] = 'awaiting';
		$insert['sendstatus']    = null;
		$insert['amount']        = null;
		$insert['answertext']    = null;
		$insert['group_id']      = null;
		$insert['recommend_id']  = null;

		self::ready_to_update_or_insert($insert);

		self::check_need_analyze($insert);

		$id = self::add_update($insert);


		if($insert['group_id'])
		{
			\lib\db\smsgroup::update_group_count($insert['group_id']);
		}

		if($id)
		{
			\dash\log::set('apiSmsAppNewSaved', ['code' => $id]);
			\dash\notif::ok(T_("Message saved"));
			return ['smsid' => \dash\coding::encode($id)];
		}

		\dash\log::set('apiSmsAppCanNotSave');
		return false;
	}


	private static function ready_to_update_or_insert(&$_insert)
	{
		$fromnumber   = $_insert['fromnumber'];
		$get_last_sms = \lib\db\sms::get_last_sms($fromnumber);

		if(isset($get_last_sms['date']))
		{
			$date = $get_last_sms['date'];

			if(abs(strtotime($_insert['date']) - strtotime($date)) < 5)
			{
				$id             = $get_last_sms['id'];
				$text           = $get_last_sms['text'];

				if($_insert['text'] === $text)
				{
					// duplicate message
					// my son is send some request in one time
					// we check it to not save duplicate message :|
					\dash\log::set('apiSmsAppDuplicateNewMessage');
					self::$sms_id = intval($get_last_sms['id']);
					self::$update_insert = 'non';
					return;
				}

				$new_text           = $text. $_insert['text'];

				// $update             = [];
				$_insert['text']     = $new_text;
				$_insert['smscount'] = mb_strlen($new_text);

				if(!$get_last_sms['group_id'] && $_insert['group_id'])
				{
					$_insert['group_id'] = $_insert['group_id'];
				}

				if($get_last_sms['receivestatus'] === 'block')
				{
					// nothing
				}
				else
				{
					$get_recommend = \lib\app\sms::analyze_text($new_text);

					if(!$get_recommend)
					{
						// reset
						$_insert['recommend_id']    = null;
						$_insert['group_id']        = null;
						$_insert['sendstatus']      = null;
						$_insert['answertext']      = null;
						$_insert['answertextcount'] = null;
						$_insert['receivestatus']   = 'awaiting';
						$_insert['fromgateway']     = null;
						$_insert['tonumber']        = null;
						$_insert['dateanswer']      = null;
					}
					else
					{
						if(!$get_last_sms['recommend_id'] && $_insert['recommend_id'])
						{
							$_insert['recommend_id'] = $_insert['recommend_id'];
						}
					}
				}

				self::$update_insert = 'update';
				self::$sms_id = $get_last_sms['id'];
				return;
			}
		}
	}


	private static function add_update($_insert)
	{
		if(self::$update_insert === 'update' && self::$sms_id)
		{
			\lib\db\sms::update($_insert, self::$sms_id);
			return intval(self::$sms_id);
		}
		elseif(self::$update_insert === 'insert')
		{
			$id = \lib\db\sms::insert($_insert);
			return $id;
		}
	}


	// private static function check_add_update($_insert)
	// {
	// 	$fromnumber   = $_insert['fromnumber'];
	// 	$get_last_sms = \lib\db\sms::get_last_sms($fromnumber);

	// 	if(isset($get_last_sms['date']))
	// 	{
	// 		$date = $get_last_sms['date'];
	// 		if(abs(strtotime($_insert['date']) - strtotime($date)) < 5)
	// 		{
	// 			$id             = $get_last_sms['id'];
	// 			$text           = $get_last_sms['text'];

	// 			if($_insert['text'] === $text)
	// 			{
	// 				// duplicate message
	// 				// my son is send some request in one time
	// 				// we check it to not save duplicate message :|
	// 				\dash\log::set('apiSmsAppDuplicateNewMessage');
	// 				return intval($get_last_sms['id']);
	// 			}

	// 			$new_text           = $text. $_insert['text'];

	// 			$update             = [];
	// 			$update['text']     = $new_text;
	// 			$update['smscount'] = mb_strlen($new_text);

	// 			if(!$get_last_sms['group_id'] && $_insert['group_id'])
	// 			{
	// 				$update['group_id'] = $_insert['group_id'];
	// 			}

	// 			if($get_last_sms['receivestatus'] === 'block')
	// 			{
	// 				// nothing
	// 			}
	// 			else
	// 			{
	// 				$get_recommend = \lib\app\sms::analyze_text($new_text);

	// 				if(!$get_recommend)
	// 				{
	// 					// reset
	// 					$update['recommend_id']    = null;
	// 					$update['group_id']        = null;
	// 					$update['sendstatus']      = null;
	// 					$update['answertext']      = null;
	// 					$update['answertextcount'] = null;
	// 					$update['receivestatus']   = 'awaiting';
	// 					$update['fromgateway']     = null;
	// 					$update['tonumber']        = null;
	// 					$update['dateanswer']      = null;
	// 				}
	// 				else
	// 				{
	// 					if(!$get_last_sms['recommend_id'] && $_insert['recommend_id'])
	// 					{
	// 						$update['recommend_id'] = $_insert['recommend_id'];
	// 					}
	// 				}
	// 			}


	// 			\lib\db\sms::update($update, $get_last_sms['id']);
	// 			return intval($get_last_sms['id']);
	// 		}
	// 	}

	// 	$id = \lib\db\sms::insert($_insert);
	// 	return $id;
	// }


	private static function check_need_analyze(&$insert)
	{
		$number       = $insert['fromnumber'];
		$mobileNumber = \dash\utility\filter::mobile($number);
		$number_no_plus = str_replace("+", '', $number);

		if($mobileNumber)
		{
			$get = \lib\db\smsgroupfilter::get(['type' => 'number', '1.1' => ["= 1.1", "AND (`number` = '$number' OR `number` = '$number_no_plus' OR `number` = '$mobileNumber')"], 'limit' => 1]);
		}
		else
		{
			$get = \lib\db\smsgroupfilter::get(['type' => 'number', '1.1' => [" = 1.1" , "AND ( `number` = '$number' OR `number` = '$number_no_plus') "], 'limit' => 1]);
		}


		// this number not found in any filter
		if(isset($get['group_id']))
		{

			$insert['group_id'] = $get['group_id'];

			$get_group = \lib\db\smsgroup::get(['id' => $get['group_id'], 'limit' => 1]);

			if(isset($get_group['status']) && $get_group['status'] === 'enable')
			{
				if(array_key_exists('analyze', $get_group) && !$get_group['analyze'])
				{
					$insert['receivestatus']  = 'block';
					// if the message is block not check recommend
					return;
				}
			}
		}

		$text          = $insert['text'];
		$get_recommend = \lib\app\sms::analyze_text($text);

		if(isset($get_recommend['id']))
		{
			$insert['recommend_id'] = $get_recommend['id'];

			// ready to auto answer
			$load_default_answer = \lib\db\smsgroupfilter::get(['type' => 'answer', 'group_id' => $get_recommend['id'], 'isdefault' => 1, 'limit' => 1]);
			if(isset($load_default_answer['text']))
			{
				$insert['sendstatus']      = 'waitingtoautosend';
				$insert['answertext']      = $load_default_answer['text'];
				$insert['answertextcount'] = mb_strlen($load_default_answer['text']);
				$insert['receivestatus']   = 'answerready';
				$insert['fromgateway']     = $insert['togateway'];
				$insert['tonumber']        = $insert['fromnumber'];
				$insert['group_id']        = $insert['recommend_id'];
				$insert['dateanswer']      = date("Y-m-d H:i:s");
			}
		}
	}


	private static function fixText($_text)
	{
		$_text = strip_tags($_text);
		$_text = str_replace('[', ' ', $_text);
		$_text = str_replace(']', ' ', $_text);
		$_text = str_replace('{', ' ', $_text);
		$_text = str_replace('}', ' ', $_text);
		$_text = str_replace('"', ' ', $_text);
		$_text = str_replace('؛', ' ', $_text);
		$_text = str_replace("'", ' ', $_text);
		$_text = str_replace('(', ' ', $_text);
		$_text = str_replace(')', ' ', $_text);
		$_text = str_replace(':', ' ', $_text);
		$_text = str_replace(',', ' ', $_text);
		$_text = str_replace('،', ' ', $_text);
		$_text = str_replace('-', ' ', $_text);
		$_text = str_replace('_', ' ', $_text);
		$_text = str_replace('?', ' ', $_text);
		$_text = str_replace('؟', ' ', $_text);
		$_text = str_replace('.', ' ', $_text);
		$_text = str_replace('=', ' ', $_text);
		$_text = str_replace('
', ' ', $_text);

		$_text = str_replace("\n", ' ', $_text);
		$_text = str_replace('!', ' ', $_text);
		$_text = str_replace('&nbsp;', ' ', $_text);
		return trim($_text);
	}
}
?>