<?php
namespace content_smsapp\listsms;


class model
{
	public static function post()
	{
		if(\dash\request::post('type') === 'setAnswer')
		{
			$answer_id    = \dash\request::post('answer_id');
			$recommend_id = \dash\request::post('recommend_id');
			$fromgateway  = \dash\request::post('fromgateway');
			// from_sender
			// from_smspanel
			if(!$answer_id)
			{
				\dash\notif::error(T_("Please choose one answer"));
				return false;
			}
			\lib\app\sms::recommend_answer($recommend_id, $answer_id, $fromgateway);
			\dash\notif::ok(T_("All message group saved"));
			\dash\redirect::pwd();
			return;
		}

		if(\dash\request::post('recommend') === 'invalid')
		{
			$post                 = [];
			$post['recommend_id'] = null;
			$result               = \lib\app\sms::edit($post, \dash\request::post('id'));
			\dash\redirect::pwd();
			return;
		}

		if(\dash\request::post('skip') === 'skip')
		{
			$post                  = [];
			$post['group_id']      = null;
			$post['answertext']    = null;
			$post['sendstatus']    = null;
			$post['dateanswer']    = date("Y-m-d H:i:s");
			$post['receivestatus'] = 'skip';

			$result = \lib\app\sms::edit($post, \dash\request::post('id'));

			\dash\redirect::pwd();
			return;
		}
		$status = \dash\request::post('status');

		if($status === 'change')
		{
			$myStatus = \lib\app\sms::status();
			if($myStatus)
			{
				$myStatus = \lib\app\sms::status(false);
			}
			else
			{
				$myStatus = \lib\app\sms::status(true);
			}

			\dash\redirect::pwd();
		}

	}
}
?>
