<?php
namespace content_smsapp\conversation\view;


class view
{
	public static function config()
	{
		\dash\data::page_pictogram('chat');

		\dash\data::page_title('نمایش لیست گفتگو با '. \dash\fit::text(\dash\data::myMobile()));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back'));


		$filterArray = [];
		$countArgs   = [];

		$args =
		[
			'order'      => \dash\request::get('order'),
			'sort'       => \dash\request::get('sort'),
			'limit'      => 100,
			'fromnumber' => \dash\data::myMobile(),
		];

		$q = \dash\request::get('q');

		$list = \lib\app\conversation\search::view($q, $args);


		if(a($list, 0, 'answertext'))
		{
			\dash\data::lockAnswer(true);
		}

		\dash\data::dataTable($list);

		$need_archive = array_column($list, 'conversation_answered');
		$need_archive = array_unique($need_archive);
		// $need_archive = array_filter($need_archive);
		$need_archive = array_values($need_archive);

		if(in_array(null, $need_archive))
		{
			\dash\data::needArchive(true);
		}

		$smsgroup = \lib\db\smsgroup::get_answering_group();

		\dash\data::groupList($smsgroup);

		$answers = \lib\db\smsgroupfilter::get(['type' => 'answer']);
		$dataAnswer = [];
		if(is_array($answers))
		{
			foreach ($answers as $key => $value)
			{
				if(!isset($dataAnswer[$value['group_id']]))
				{
					$dataAnswer[$value['group_id']] = [];
				}

				$dataAnswer[$value['group_id']][] = $value;
			}
		}

		\dash\data::dataAnswer($dataAnswer);


		$currentUser = \lib\app\conversation\search::load_current_user($list);
		\dash\data::currentUser($currentUser);


		$block_group_id = \content_smsapp\editgroup\controller::block_group_id(true);
		$secret_group_id = \content_smsapp\editgroup\controller::secret_group_id(true);


		$find_blocked = \lib\db\smsgroupfilter::get(['type' => 'number', 'number' => \dash\data::myMobile(), 'group_id' => ['IN', "($block_group_id, $secret_group_id)"]]);
		if(!is_array($find_blocked))
		{
			$find_blocked = [];
		}

		foreach ($find_blocked as $key => $value)
		{
			if(isset($value['group_id']))
			{
				if(floatval($value['group_id']) === floatval($block_group_id))
				{
					\dash\data::isBlock(\lib\app\smsgroupfilter::ready($value));
				}
				elseif(floatval($value['group_id']) === floatval($secret_group_id))
				{
					\dash\data::isSecred(\lib\app\smsgroupfilter::ready($value));

				}
			}
		}
	}
}
?>
