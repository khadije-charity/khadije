<?php
namespace lib\app;

class travel
{
	public static $sort_field =
	[
		'id',
		'place',
		'countpeople',
		'type',
		'status',
	];


	public static $cityplace_cat = 'city_place';


	public static function city_list($_trans = false)
	{
		$city =
		[
			($_trans ? T_("Karbala") : "karbala"),
			($_trans ? T_("Mashhad") : "mashhad"),
			($_trans ? T_("Qom") 	 : "qom"),
		];

		return $city;
	}



	public static function group_active_city()
	{
		$get_detail =
		[
			'cat'    => 'group_city',
			'status' => 'enable',
		];

		$get = \lib\db\options::get($get_detail);

		$city_list = [];

		if(is_array($get))
		{
			$temp = array_column($get, 'value');
			foreach ($temp as $key => $value)
			{
				$city_list[$value] = T_($value);
			}
		}

		return $city_list;
	}


	public static function active_city()
	{
		$get_detail =
		[
			'cat'    => 'trip_city',
			'status' => 'enable',
		];

		$get = \lib\db\options::get($get_detail);

		$city_list = [];

		if(is_array($get))
		{
			$temp = array_column($get, 'value');
			foreach ($temp as $key => $value)
			{
				$city_list[$value] = T_($value);
			}
		}

		return $city_list;
	}


	public static function save_option_setting($_cat, $_key, $_value, $_action)
	{
		if(!$_action || $_action == '')
		{
			$_action = false;
		}
		else
		{
			$_action = true;
		}

		$get_detail =
		[
			'cat'   => $_cat,
			'key'   => $_key,
			'limit' => 1,
		];

		$get = \lib\db\options::get($get_detail);

		if(isset($get['id']))
		{
			$update = true;
		}
		else
		{
			$update = false;
		}

		if($_action)
		{
			if($update)
			{
				\lib\db\options::update(['status' => 'enable', 'value' => $_value], $get['id']);
			}
			else
			{
				$insert_new =
				[
					'cat'    => $_cat,
					'key'    => $_key,
					'value'  => $_value,
					'status' => 'enable',
				];
				\lib\db\options::insert($insert_new);
			}
		}
		else
		{
			if($update)
			{
				\lib\db\options::update(['status' => 'disable', 'value' => $_value], $get['id']);
			}
			else
			{
				$insert_new =
				[
					'cat'    => $_cat,
					'key'    => $_key,
					'value'  => $_value,
					'status' => 'disable',
				];
				\lib\db\options::insert($insert_new);
			}
		}
	}


	public static function city_signup_setting($_city, $_action)
	{

		if(!in_array($_city, self::city_list()))
		{
			\lib\notif::error(T_("Invalid city"));
			return false;
		}

		self::save_option_setting('trip_city', 'trip_city_'. $_city, $_city, $_action);

	}


	public static function trip_master_active($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'trip_settings', 'key' => 'trip_master_active', 'value' => 'trip_master_active', 'limit' => 1]);
			if(isset($get['status']) && $get['status'] === 'enable')
			{
				return true;
			}
			return false;
		}
		else
		{
			self::save_option_setting('trip_settings', 'trip_master_active', 'trip_master_active', $_action);
		}
	}


	public static function trip_count_partner($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'trip_settings', 'key' => 'trip_count_partner', 'limit' => 1]);
			if(isset($get['value']) && $get['value'] )
			{
				return $get['value'];
			}
			return false;
		}
		else
		{
			self::save_option_setting('trip_settings', 'trip_count_partner', $_action, $_action);
		}
	}

	public static function trip_max_awaiting($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'trip_settings', 'key' => 'trip_max_awaiting', 'limit' => 1]);
			if(isset($get['value']) && $get['value'])
			{
				return $get['value'];
			}
			return false;
		}
		else
		{
			self::save_option_setting('trip_settings', 'trip_max_awaiting', $_action, $_action);
		}
	}

	//----------------------------------------------------------------------------------------------------

	public static function group_city_signup_setting($_city, $_action)
	{

		if(!in_array($_city, self::city_list()))
		{
			\lib\notif::error(T_("Invalid city"));
			return false;
		}

		self::save_option_setting('group_city', 'group_city_'. $_city, $_city, $_action);

	}


	public static function group_master_active($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'group_settings', 'key' => 'group_master_active', 'value' => 'group_master_active', 'limit' => 1]);
			if(isset($get['status']) && $get['status'] === 'enable')
			{
				return true;
			}
			return false;
		}
		else
		{
			self::save_option_setting('group_settings', 'group_master_active', 'group_master_active', $_action);
		}
	}


	public static function group_count_partner_min($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'group_settings', 'key' => 'group_count_partner_min', 'limit' => 1]);
			if(isset($get['value']) && $get['value'] )
			{
				return $get['value'];
			}
			return false;
		}
		else
		{
			self::save_option_setting('group_settings', 'group_count_partner_min', $_action, $_action);
		}
	}

	public static function group_count_partner_max($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'group_settings', 'key' => 'group_count_partner_max', 'limit' => 1]);
			if(isset($get['value']) && $get['value'] )
			{
				return $get['value'];
			}
			return false;
		}
		else
		{
			self::save_option_setting('group_settings', 'group_count_partner_max', $_action, $_action);
		}
	}


	public static function group_max_awaiting($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'group_settings', 'key' => 'group_max_awaiting', 'limit' => 1]);
			if(isset($get['value']) && $get['value'])
			{
				return $get['value'];
			}
			return false;
		}
		else
		{
			self::save_option_setting('group_settings', 'group_max_awaiting', $_action, $_action);
		}
	}


	public static function group_getdate($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'group_settings', 'key' => 'group_getdate', 'value' => 'group_getdate', 'limit' => 1]);
			if(isset($get['status']) && $get['status'] === 'enable')
			{
				return true;
			}
			return false;
		}
		else
		{
			self::save_option_setting('group_settings', 'group_getdate', 'group_getdate', $_action);
		}
	}



	//----------------------------------------------------------------------------------------------------

	public static function trip_getdate($_action = 'get')
	{
		if($_action === 'get')
		{
			$get = \lib\db\options::get(['cat' => 'trip_settings', 'key' => 'trip_getdate', 'value' => 'trip_getdate', 'limit' => 1]);
			if(isset($get['status']) && $get['status'] === 'enable')
			{
				return true;
			}
			return false;
		}
		else
		{
			self::save_option_setting('trip_settings', 'trip_getdate', 'trip_getdate', $_action);
		}
	}


	public static function remove_cityplace($_id)
	{
		if($_id && is_numeric($_id) && \lib\db\myoption::remove($_id))
		{
			return true;
		}

		return false;
	}


	public static function user_travel_list($_type = 'family')
	{
		$user_id = \lib\user::id();
		if(!$user_id)
		{
			return false;
		}

		$travele_list = \lib\db::get("SELECT * FROM travels WHERE `user_id` = $user_id AND type = '$_type' ORDER BY id DESC LIMIT 50 ");

		return $travele_list;
	}


	public static function cityplace_list()
	{
		$list = \lib\db\options::get(['cat' => self::$cityplace_cat]);

		$cityplace_list = [];

		if(is_array($list))
		{
			foreach ($list as $key => $value)
			{
				if(isset($value['value']) && isset($value['meta']) && isset($value['status']) && $value['status'] === 'enable' && isset($value['id']))
				{
					$cityplace_list[] = ['city' => $value['value'], 'place' => $value['meta'], 'id' => $value['id']];
				}
			}
		}

		return $cityplace_list;
	}



	/**
	 * Gets the product.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The product.
	 */
	public static function list($_string = null, $_args = [])
	{
		if(!\lib\user::id())
		{
			return false;
		}

		$default_args =
		[
			'order' => null,
			'sort'  => null,
			'in'    => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$option             = [];
		$option             = array_merge($default_args, $_args);

		if($option['order'])
		{
			if(!in_array($option['order'], ['asc', 'desc']))
			{
				unset($option['order']);
			}
		}

		if($option['sort'])
		{
			if(!in_array($option['sort'], self::$sort_field))
			{
				unset($option['sort']);
			}
		}

		$field             = [];

		if($option['in'] && $_string && in_array($option['in'], ['status', 'place', 'type']))
		{
			$option['travels.'.$option['in']] = $_string;
			$_string = null;
		}
		unset($option['in']);

		$result = \lib\db\travels::search($_string, $option, $field);

		return $result;
	}


	public static function set_cityplace($_city, $_place)
	{
		$cat = self::$cityplace_cat;

		if(mb_strlen($_city) > 50)
		{
			\lib\notif::error(T_("City name must be less than 50 character"), 'city');
			return false;
		}

		if(mb_strlen($_place) > 50)
		{
			\lib\notif::error(T_("Place name must be less than 50 character"), 'place');
			return false;
		}

		if(!$_city)
		{
			\lib\notif::error(T_("Please fill the city name"), 'city');
			return false;
		}


		if(!$_place)
		{
			\lib\notif::error(T_("Please fill the place name"), 'place');
			return false;
		}

		if(!in_array($_city, self::city_list()))
		{
			\lib\notif::error(T_("Please select city from the list"), 'city');
			return false;
		}

		$list = \lib\db\options::get(['cat' => $cat]);

		if(\lib\db\myoption::check_city_place_duplicate($_city, $_place, $cat))
		{
			\lib\notif::error(T_("Duplicate place in one city"), 'city');
			return false;
		}

		$insert_args =
		[
			'cat'   => $cat,
			'key'   => null,
			'value' => $_city,
			'meta'  => $_place,
		];
		\lib\db\options::insert($insert_args);
	}


	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	private static function check($_option = [])
	{
		$default_option =
		[
			'debug' => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		$city = \lib\app::request('city');
		if(\lib\app::request('type') === 'group')
		{
			if(!$city || !in_array(T_($city), self::group_active_city()))
			{
				\lib\notif::error(_("Invalid city"), 'city');
				return false;
			}
		}
		else
		{
			if(!$city || !in_array(T_($city), self::active_city()))
			{
				\lib\notif::error(_("Invalid city"), 'city');
				return false;
			}
		}


		$startdate = \lib\app::request('startdate');
		$startdate = \lib\utility\convert::to_en_number($startdate);

		if($startdate && strtotime($startdate) === false)
		{
			\lib\notif::error(_("Invalid parameter startdate"), 'startdate');
			return false;
		}

		if($startdate)
		{
			$startdate = date("Y-m-d", strtotime($startdate));
		}

		$enddate = \lib\app::request('enddate');
		$enddate = \lib\utility\convert::to_en_number($enddate);

		if($enddate && strtotime($enddate) === false)
		{
			\lib\notif::error(_("Invalid parameter enddate"), 'enddate');
			return false;
		}

		if($enddate)
		{
			$enddate = date("Y-m-d", strtotime($enddate));
		}

		$args              = [];
		$args['place']     = $city;
		$args['startdate'] = $startdate;
		$args['enddate']   = $enddate;

		return $args;
	}


	/**
	 * add new product
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args, $_option = [])
	{
		$default_option =
		[
			'debug' => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		\lib\app::variable($_args);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		if(!\lib\user::id())
		{
			\lib\app::log('api:product:user_id:notfound', null, $log_meta);
			if($_option['debug']) \lib\notif::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check($_option);

		if($args === false || !\lib\notif::$status)
		{
			return false;
		}

		$check_duplicate_travel = \lib\db\travels::get(['user_id' => \lib\user::id(), 'type' => \lib\app::request('type'), 'place' => $args['place'], 'status' => ["IN", "('draft', 'awaiting')"], 'limit' => 1]);
		if(isset($check_duplicate_travel['id']))
		{
			\lib\notif::error(T_("You signup to this trip before, please wait for checking status of that trip"));
			return false;
		}

		if(!isset($args['status']) || (isset($args['status']) && !$args['status']))
		{
			$args['status']  = 'draft';
		}

		$args['user_id']     = \lib\user::id();
		$args['type']        = \lib\app::request('type');

		$travel_id = \lib\db\travels::insert($args);

		if(!$travel_id)
		{
			\lib\notif::error(T_("No way to add travel"));
			return false;
		}

		return $travel_id;
	}
}
?>