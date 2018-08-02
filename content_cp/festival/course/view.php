<?php
namespace content_cp\festival\course;


class view
{
	public static function config()
	{
		\dash\data::display_festivalCourseDisplay('content_cp/festival/course/list.html');

		\dash\data::badge_link(\dash\url::here(). '/festival?id='. \dash\request::get('id'));
		\dash\data::badge_text(T_('Back to dashboard'));

		if(\dash\request::get('type') === 'add')
		{
			\dash\permission::access('festivalCourseAdd');
			\dash\data::display_festivalCourseDisplay('content_cp/festival/course/add.html');
			\dash\data::page_title(\dash\data::currentFestival_title(). ' | '. T_("Add new course"));
			\dash\data::page_desc(T_("Add new course by some detail"));
			\dash\data::page_pictogram('plus');


		}
		else
		{

			\dash\permission::access('fpFestivalView');

			\dash\data::page_pictogram('arrows-alt');

			\dash\data::page_title(\dash\data::currentFestival_title(). ' | '. T_("Course list"));

			\dash\data::page_desc(T_("check festival course and add or edit a course"));

			$args                = [];
			$args['festival_id'] = \dash\coding::decode(\dash\request::get('id'));
			$args['pagenation']  = false;

			$dataTable = \lib\app\festivalcourse::list(null, $args);

			\dash\data::dataTable($dataTable);
		}
	}
}
?>
