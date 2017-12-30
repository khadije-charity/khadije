<?php
namespace content_a\travel\home;


class view extends \content_a\main\view
{
	public function config()
	{
		$this->data->page['title']   = T_("Khadije Dashboard");
		$this->data->page['desc']    = T_("Glance at your stores and quickly navigate to stores.");
		$this->data->page['special'] = true;

		$this->data->child_list = \lib\db\users::get(['parent' => \lib\user::id()]);
		$this->data->cityplace_list = \lib\app\travel::cityplace_list();

	}
}
?>
