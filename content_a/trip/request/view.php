<?php
namespace content_a\trip\request;


class view extends \content_a\main\view
{
	public function config()
	{
		$this->data->page['title'] = T_("Register for new trip request"). ' | '. T_('Step 1');
		$this->data->page['desc']  = T_('in 3 simple step register your request for have trip to holy places');

		$this->data->page['badge']['link'] = \dash\url::here(). '/trip';
		$this->data->page['badge']['text'] = T_('check your trip requests');

		if(!\lib\app\travel::trip_master_active('get'))
		{
			$this->data->signupLocked = true;
		}
		else
		{
			$this->data->cityplaceList = \lib\app\travel::active_city();
		}

	}
}
?>
