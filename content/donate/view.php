<?php
namespace content\donate;


class view extends \mvc\view
{
	function config()
	{
		$this->data->page['title'] = T_("Donate");
		$this->data->page['desc']  = T_("Pay your donate online with below form");


		$this->data->bodyclass = 'unselectable vflex';
		$this->data->way_list  = \lib\app\donate::way_list();
		$this->data->donateArchive = \lib\db\mytransactions::user_transaction('cash');


		if(\lib\session::get('payment_request_start'))
		{
			if(\lib\utility\payment::get_status())
			{
				\lib\utility\payment::clear_session();
				$this->data->payment_verify_msg = T_("Thanks for your payment");
				\lib\utility\donate::sms_success();
			}
			else
			{
				$this->data->payment_verify_msg = T_("Payment unsuccessfull");
			}
		}
	}
}
?>