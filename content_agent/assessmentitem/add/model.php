<?php
namespace content_agent\assessmentitem\add;


class model
{
	public static function post()
	{


		$post =
		[
			'title' => \dash\request::post('title'),
			'job'   => \dash\request::post('job'),
			'city'  => \dash\request::get('city'),
		];

		$result = \lib\app\assessmentitem::add($post);

		if(\dash\engine\process::status())
		{
			if(isset($result['id']))
			{
				$url_get            = [];
				$url_get['id']      = $result['id'];
				$url_get['city']    = \dash\request::get('city');
				$url_get['job']     = \dash\request::get('job');
				$url_get['job_for'] = \dash\request::get('job_for');

				\dash\redirect::to(\dash\url::this(). '/edit?'. http_build_query($url_get));
			}
			else
			{
				\dash\redirect::to(\dash\url::this(). \dash\data::xCityStart());
			}
		}

	}
}
?>