<?php
namespace content_cp\nationalcode\edit;


class model extends \content_cp\main2\model
{
	public function post_nationalcode()
	{
		$update            = [];
		$update['qom']     = \lib\request::post('qom');
		$update['mashhad'] = \lib\request::post('mashhad');
		$update['karbala'] = \lib\request::post('karbala');

		if($update['qom'] && !is_numeric($update['qom']) || intval($update['qom']) > 999)
		{
			\lib\debug::error(T_("Invalid qom number"), 'qom');
			return false;
		}
		if(!$update['qom'])
		{
			$update['qom'] = null;
		}


		if($update['mashhad'] && !is_numeric($update['mashhad']) || intval($update['mashhad']) > 999)
		{
			\lib\debug::error(T_("Invalid mashhad number"), 'mashhad');
			return false;
		}
		if(!$update['mashhad'])
		{
			$update['mashhad'] = null;
		}

		if($update['karbala'] && !is_numeric($update['karbala']) || intval($update['karbala']) > 999)
		{
			\lib\debug::error(T_("Invalid karbala number"), 'karbala');
			return false;
		}
		if(!$update['karbala'])
		{
			$update['karbala'] = null;
		}

		if(\lib\utility::get('id') && is_numeric(\lib\utility::get('id')))
		{
			\lib\db\nationalcodes::update($update, \lib\utility::get('id'));
			if(\lib\debug::$status)
			{
				\lib\debug::true(T_("Your change was saved"));
			}
		}
		else
		{
			\lib\debug::error(T_("Id not found"));
		}


	}
}
?>
