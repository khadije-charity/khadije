<?php
namespace content_a\donate;


class model extends \content_a\main\model
{

	public function post_donate()
	{
		$args =
		[
			'username' => \lib\utility::post('username'),
			'niyat'    => \lib\utility::post('niyat'),
			'way'      => \lib\utility::post('way'),
			'fullname' => \lib\utility::post('fullname'),
			'email'    => \lib\utility::post('email'),
			'mobile'   => \lib\utility::post('mobile'),
			'amount'   => \lib\utility::post('amount'),
		];

		\lib\app\donate::add($args);
	}
}
?>
