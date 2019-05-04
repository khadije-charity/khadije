<?php
namespace lib\tg;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;

class smsapp
{
	public static function detector($_cmd)
	{
		$myCommand = $_cmd['commandRaw'];
		if(bot::isCallback())
		{
			$myCommand = substr($myCommand, 3);
		}
		elseif(bot::isInline())
		{
			$myCommand = substr($myCommand, 3);
		}
		// remove command from start
		if(substr($myCommand, 0, 1) == '/')
		{
			$myCommand = substr($myCommand, 1);
		}
		// remove survey from start of command
		// and detect survey No
		$smsNo = null;
		if(substr($myCommand, 0, 7) === 'smsapp_')
		{
			$smsNo = substr($myCommand, 7);
		}
		else
		{
			switch ($myCommand)
			{
				case 'sms':
				case 'Sms':
				case 'SMS':
					// show list of survey
					self::sms();
					return true;
					break;

				default:
					return false;
					break;
			}
		}

		// remove botname from surveyNo if exist
		$smsNo = strtok($smsNo, '@');
		// check if survey id is not exist show list
		if(!$smsNo)
		{
			self::empty();
			return false;
		}
		// if code is not valid show related message
		if(!\dash\coding::is($smsNo))
		{
			self::requireCode();
			return false;
		}
		// detect opt
		$myCat = null;
		if(isset($_cmd['optionalRaw']) && $_cmd['optionalRaw'])
		{
			$myCat = $_cmd['optionalRaw'];
		}
		// detect arg
		$myAnswer = null;
		if(isset($_cmd['argumentRaw']) && $_cmd['argumentRaw'])
		{
			$myAnswer = $_cmd['argumentRaw'];
		}


		if(bot::isCallback())
		{
			// transfer conditions here

		}
		else
		{
			// do nothing
		}


		if($myCat && $myAnswer)
		{
			// step3
			// remove all keyboard
			return self::finishMsg($smsNo, $myCat, $myAnswer);
		}
		elseif($myCat)
		{
			// step2
			// show answer btn
			return self::fillAnswers($smsNo, $myCat);
		}
		elseif($myCat === null)
		{
			// step1
			// show cat btn
			return self::fillCats($smsNo);
		}
	}



	public static function fillCats($_surveyId)
	{
		bot::ok();

		$surveyTxt    = \lib\app\tg\survey::get($_surveyId);
		$surveyStatus = \lib\app\tg\survey::$status;

		if($surveyTxt)
		{
			$result =
			[
				'text'         => $surveyTxt,
				'reply_markup' =>
				[
					'inline_keyboard' =>
					[
						[
							[
								'text'          => 	T_("Share"),
								'switch_inline_query' => $_surveyId,
							],
						],
						[
							[
								'text' => T_("Answer via site"),
								'url'  => \dash\url::base(). '/s/'. $_surveyId,
							],
						],
						[
							[
								'text'          => 	T_("Start"),
								'callback_data' => 'survey_'. $_surveyId. ' start',
							],
						],
					]
				]
			];

			if($surveyStatus !== 'publish')
			{
				// remove first keyboard to share survey if its not published
				unset($result['reply_markup']['inline_keyboard'][0][0]);
			}

			if(!bot::isPrivate())
			{
				$result['reply_markup']['inline_keyboard'][1] =
				[
					[
						'text' => T_("Answer via bot"),
						'url'  => bot::deepLink('survey_'. $_surveyId)
					],
				];
			}

			// if start with callback answer callback
			if(bot::isCallback())
			{
				$callbackResult =
				[
					'text' => T_("Survey"). ' '. $_surveyId,
				];
				bot::answerCallbackQuery($callbackResult);
			}

			bot::sendMessage($result);
		}
		else
		{
			if(bot::isCallback())
			{
				$callbackResult =
				[
					'text' => T_("We can't find detail of this survey!"),
					'show_alert' => true,
				];
				bot::answerCallbackQuery($callbackResult);
			}
		}
	}


	public static function finishMsg($_surveyId)
	{
		bot::ok();

		$surveyTxt = \lib\app\tg\survey::get($_surveyId, 'thankyou');

		if($surveyTxt)
		{
			$result =
			[
				'text'         => $surveyTxt,
				// 'reply_markup' =>
				// [
				// 	'inline_keyboard' =>
				// 	[
				// 		[
				// 			[
				// 				'text' => T_("Sarshomar website"),
				// 				'url'  => bot::website(),
				// 			],
				// 		]
				// 	]
				// ]
			];
			// // remove keyboard of old messages
			// $newMsg =
			// [
			// 	'reply_markup' =>
			// 	[
			// 		'inline_keyboard' =>
			// 		[
			// 			[
			// 				[
			// 					'text' => T_("Sarshomar website"),
			// 					'url'  => bot::website(),
			// 				],
			// 			]
			// 		]
			// 	]
			// ];
			// bot::editMessageReplyMarkup($newMsg);
			$result['reply_markup'] = \lib\tg\detect::mainmenu(true);

			// if start with callback answer callback
			if(bot::isCallback())
			{
				$callbackResult =
				[
					'text' => T_("Survey"). ' '. $_surveyId,
				];
				bot::answerCallbackQuery($callbackResult);
			}

			bot::sendMessage($result);
		}
		else
		{
			if(bot::isCallback())
			{
				$callbackResult =
				[
					'text' => T_("We can't find detail of this survey!"),
					'show_alert' => true,
				];
				bot::answerCallbackQuery($callbackResult);
			}
		}
	}

	public static function requireCode()
	{
		bot::ok();
		$msg = T_("We need sms code!")." 🙁";

		// if start with callback answer callback
		if(bot::isCallback())
		{
			$callbackResult =
			[
				'text' => $msg,
			];
			bot::answerCallbackQuery($callbackResult);
		}

		$result =
		[
			'text' => $msg,
		];
		bot::sendMessage($result);
	}


	public static function empty()
	{
		bot::ok();
		$msg = T_("SMS number is invalid!")." 🙁";

		// if start with callback answer callback
		if(bot::isCallback())
		{
			$callbackResult =
			[
				'text' => $msg,
			];
			bot::answerCallbackQuery($callbackResult);
		}

		$result =
		[
			'text' => $msg,
		];
		bot::sendMessage($result);
	}


	public static function sms()
	{
		$surveyListTxt = \lib\app\tg\survey::list();

		if($surveyListTxt)
		{
			bot::ok();

			// if start with callback answer callback
			if(bot::isCallback())
			{
				bot::answerCallbackQuery(T_("List of your survey in Sarshomar"));
			}

			bot::sendMessage($surveyListTxt);
			return true;
		}
		else
		{
			// self::howto();
		}
	}

}
?>