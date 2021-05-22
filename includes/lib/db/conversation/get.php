<?php
namespace lib\db\conversation;

class get
{

	public static function last_record_mobile($_mobile)
	{
		$query = "SELECT  * FROM s_sms WHERE s_sms.fromnumber = '$_mobile' ORDER BY s_sms.id DESC LIMIT 1";
		$result = \dash\db::get($query, null, true);
		return $result;
	}


	public static function count_all()
	{
		$query = "  EXPLAIN  SELECT  COUNT(DISTINCT s_sms.mobile_id) AS `count` FROM s_sms ";
		$result = \dash\db::query($query);
		var_dump($result);exit();
		return $result;
	}

	public static function count_awaiting()
	{
		$query = "  EXPLAIN  SELECT  COUNT(DISTINCT s_sms.mobile_id) AS `count` FROM s_sms WHERE s_sms.receivestatus = 'block' ";
		$result = \dash\db::get($query, 'count', true);
		return $result;
	}

	public static function count_answered()
	{
		$query = "  EXPLAIN  SELECT  COUNT(DISTINCT s_sms.mobile_id) AS `count` FROM s_sms WHERE s_sms.receivestatus = 'skip' ";
		$result = \dash\db::get($query, 'count', true);
		return $result;
	}

}
?>