<?php
namespace content\khadem;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Register for new service request"));
		\dash\data::page_desc(T_('in 3 simple step register your request for have service to holy places'));

		\dash\data::serviceNeedList(\lib\db\needs::get(['type' => 'expertise', 'status' => 'enable']));
	}
}
?>
