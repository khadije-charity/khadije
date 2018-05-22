<?php
namespace content_cp\trip;


class view
{
	public static function config()
	{
		\dash\permission::access('cpTripView');

		\dash\data::page_title(T_("Request list"));
		\dash\data::page_desc(T_("check request and update status of each request"));
		\dash\data::badge_link(\dash\url::here(). '/trip/options');
		\dash\data::badge_text(T_('Options'));

		$export_link = ' <a href="'. \dash\url::here(). '/trip?export=true">'. T_("Export"). '</a>';
		\dash\data::page_desc(\dash\data::page_desc(). $export_link);

		\dash\data::bodyclass('unselectable');
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);

		$args =
		[
			'order'          => \dash\request::get('order'),
			'sort'           => \dash\request::get('sort'),
		];

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if(\dash\request::get('status')) $args['travels.status']         = \dash\request::get('status');
		if(\dash\request::get('type')) $args['travels.type']             = \dash\request::get('type');
		if(\dash\request::get('place')) $args['travels.place']           = \dash\request::get('place');
		if(\dash\request::get('gender')) $args['users.gender']           = \dash\request::get('gender');
		if(\dash\request::get('birthday')) $args['YEAR(users.birthday)'] = \dash\request::get('birthday');

		$in = [];
		if(\dash\permission::check('cpTripQom'))
		{
			array_push($in, 'qom');
		}

		if(\dash\permission::check('cpTripMashhad'))
		{
			array_push($in, 'mashhad');
		}

		if(\dash\permission::check('cpTripKarbala'))
		{
			array_push($in, 'karbala');
		}

		$implode_in = "('". implode("','", $in). "')";

		if(isset($args['travels.place']))
		{
			if(!in_array($args['travels.place'], $in))
			{
				$args['travels.place'] = ["IN", $implode_in];
			}
		}
		else
		{
			$args['travels.place'] = ["IN", $implode_in];
		}

		if(!isset($args['travels.status']))
		{
			$args['travels.status'] = ["NOT IN", "('cancel', 'draft')"];
		}

		$search_string            = \dash\request::get('q');

		if($search_string)
		{
			\dash\data::page_title(T_('Search'). ' '.  $search_string);
		}

		$export = false;
		if(\dash\request::get('export') === 'true')
		{
			$export = true;
			$args['pagenation'] = false;
		}

		\dash\data::dataTable(\lib\app\travel::list($search_string, $args));

		if($export)
		{
			\dash\utility\export::csv(['name' => 'export_trip', 'data' => \dash\data::dataTable()]);
		}

		\dash\data::sortLink(\content_cp\view::make_sort_link(\lib\app\travel::$sort_field, \dash\url::here(). '/trip'));
		$filterArray = $args;

		if(isset($filterArray['travels.status']) && is_array($filterArray['travels.status']))
		{
			unset($filterArray['travels.status']);
		}

		if(isset($filterArray['travels.place']) && is_array($filterArray['travels.place']))
		{
			unset($filterArray['travels.place']);
		}

		if(isset($filterArray['travels.place']))
		{
			$filterArray[T_("Place")] = $filterArray['travels.place'];
			unset($filterArray['travels.place']);
		}

		if(isset($filterArray['travels.status']))
		{
			$filterArray[T_("Status")] = $filterArray['travels.status'];
			unset($filterArray['travels.status']);
		}

		if(isset($filterArray['travels.type']))
		{
			$filterArray[T_("Type")] = $filterArray['travels.type'];
			unset($filterArray['travels.type']);
		}

		if(isset($filterArray['YEAR(users.birthday)']))
		{
			$filterArray[T_("Birth year")] = $filterArray['YEAR(users.birthday)'];
			unset($filterArray['YEAR(users.birthday)']);
		}

		if(isset($filterArray['users.gender']))
		{
			$filterArray[T_("Gender")] = $filterArray['users.gender'];
			unset($filterArray['users.gender']);
		}



		// set dataFilter
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		\dash\data::dataFilter($dataFilter);

	}
}
?>
