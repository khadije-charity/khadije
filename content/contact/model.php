<?php
namespace content\contact;

class model extends \mvc\model
{

	// log callers
	// user:send:contact
	// user:send:contact:fail
	// user:send:contact:empty:message
	// user:send:contact:empty:mobile
	// user:send:contact:wrong:captha
	// user:send:contact:register:by:mobile

	/**
	 * save contact form
	 */
	public function post_contact()
	{
		if(\dash\request::post('username'))
		{
			\dash\notif::error(T_("Whate are you doing?"));
			return false;
		}

		$count = \dash\session::get('count_fill_contact');
		if($count)
		{
			\dash\session::set('count_fill_contact', $count + 1, null, 60 * 60);
		}
		else
		{
			\dash\session::set('count_fill_contact', 1, null, 60 * 60);
		}

		if($count >= 3)
		{
			\dash\notif::warn(T_("How are you?"). ":)");
			return false;
		}

		// check login
		if(\dash\user::login())
		{
			$user_id = \dash\user::id();

			// get mobile from user login session
			$mobile = \dash\user::login('mobile');

			if(!$mobile)
			{
				$mobile = \dash\request::post('mobile');
			}

			// get display name from user login session
			$displayname = \dash\user::login("displayname");
			// user not set users display name, we get display name from contact form
			if(!$displayname)
			{
				$displayname = \dash\request::post("name");
			}
			// get email from user login session
			$email = \dash\db\users::get_email($user_id);
			// user not set users email, we get email from contact form
			if(!$email)
			{
				$email = \dash\request::post("email");
			}
		}
		else
		{
			// users not registered
			$user_id     = null;
			$displayname = \dash\request::post("name");
			$email       = \dash\request::post("email");
			$mobile      = \dash\request::post("mobile");
		}
		// get the content
		$content = \dash\request::post("content");

		// save log meta
		$log_meta =
		[
			'meta' =>
			[
				'login'    => \dash\user::login('all'),
				'language' => \dash\language::current(),
				'post'     => \dash\request::post(),
			]
		];

		/**
		 * register user if set mobile and not register
		 */
		if($mobile && !\dash\user::login())
		{
			$mobile = \dash\utility\filter::mobile($mobile);

			// check valid mobile
			if($mobile)
			{
				// check existing mobile
				$exists_user = \dash\db\users::get_by_mobile($mobile);
				// register if the mobile is valid
				if(!$exists_user || empty($exists_user))
				{
					// signup user by site_guest
					$user_id = \dash\db\users::signup(['mobile' => $mobile, 'displayname' => $displayname]);
					// save log by caller 'user:send:contact:register:by:mobile'
					\dash\db\logs::set('user:send:contact:register:by:mobile', $user_id, $log_meta);
				}
				elseif(isset($exists_user['id']))
				{
					$user_id = $exists_user['id'];
				}
			}
		}

		// check content
		if($content == '')
		{
			\dash\notif::error(T_("Please try type something!"), "content");
			return false;
		}
		// ready to insert comments
		$args =
		[
			'author'  => $displayname,
			'email'   => $email,
			'type'    => 'comment',
			'content' => $content,
			'user_id' => $user_id
		];
		// insert comments
		$result = \dash\db\comments::insert($args);
		if($result)
		{
			// $mail =
			// [
			// 	'to'      => 'info@khadije.com',
			// 	'subject' => 'contact',
			// 	'body'    => $content,
			// ];
			// \dash\mail::send($mail);

			\dash\db\logs::set('user:send:contact', $user_id, $log_meta);
			\dash\notif::ok(T_("Thank You For contacting us"));
		}
		else
		{
			\dash\db\logs::set('user:send:contact:fail', $user_id, $log_meta);
			\dash\notif::error(T_("We could'nt save the contact"));
		}
	}
}