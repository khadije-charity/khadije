<?php
namespace content_smsapp\editgroup;


class view
{
	public static function config()
	{
		\dash\data::page_pictogram('server');
		\dash\data::page_title(T_("Edit sms group"));
		\dash\data::page_desc(T_("You cat set some group for sms"));

		\dash\data::badge_link(\dash\url::here(). '/settings');
		\dash\data::badge_text(T_('Settings'));



		$args =
		[
			'pagenation' => false,
			'type'       => 'analyze',
			'group_id'   => \dash\coding::decode(\dash\request::get('id')),
		];


		$tagList = \lib\app\smsgroupfilter::list(null, $args);

		if($tagList && is_array($tagList))
		{
			\dash\data::tagList($tagList);
			\dash\data::stringTagList(implode(',', array_column($tagList, 'text')));
		}

	}
}
?>
