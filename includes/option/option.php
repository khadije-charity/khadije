<?php
require_once('social.php');
require_once('payment.php');
require_once('sms.php');


/**
@ In the name Of Allah
* The base configurations of the khadije.
*/
self::$language =
[
	'default' => 'fa',
	'list'    => ['fa','en',],
];
/**
 * system default lanuage
 */
self::$config['default_language']             = 'fa';
self::$config['redirect_url']                 = 'https://khadije.com';
self::$config['multi_domain']                 = true;
self::$config['redirect_to_main']             = true;
self::$config['https']                        = true;
self::$config['default_tld']                  = 'com';
self::$config['default_permission']           = null;
self::$config['debug']                        = true;
self::$config['coming']                       = true;
self::$config['short_url']                    = null;
self::$config['save_as_cookie']               = false;
self::$config['log_visitors']                 = true;
self::$config['passphrase']                   = null;
self::$config['passkey']                      = null;
self::$config['passvalue']                    = null;
self::$config['default']                      = null;
self::$config['redirect']                     = 'a';
self::$config['register']                     = true;
self::$config['recovery']                     = true;
self::$config['fake_sub']                     = null;
self::$config['real_sub']                     = true;
self::$config['force_short_url']              = null;
self::$config['sms']                          = true;

self::$config['account']                      = true;
self::$config['main_account']                 = null;
self::$config['account_status']               = true;
self::$config['use_main_account']             = false;

self::$config['domain_same']                  = true;
self::$config['domain_name']                  = 'khadije';
self::$config['main_site']                    = 'https://khadije.com';

self::$config['favicon']['version']           = 4;




/**
 * call kavenegar template
 */
self::$config['enter']['call']                = true;
self::$config['enter']['call_template_fa'] = 'ermile-fa';
self::$config['enter']['call_template_en'] = 'ermile-en';

/**
 * first signup url
 * main redirect url . signup redirect url
 */
self::$config['enter']['singup_redirect']     = 'u';



/**
 * list of units
 */
self::$config['units'] =
[
	1 =>
	[
		'title' => 'toman',
		'desc'  => "Toman",
	],

	2 =>
	[
		'title' => 'dollar',
		'desc'  => "$",
	],
];
// the unit id for default
self::$config['default_unit'] = 1;
// force change unit to this unit
self::$config['force_unit']   = 1;

/**
 * transaction code
 */
self::$config['transactions_code'][100] = "invoice:store";
self::$config['transactions_code'][150] = "promo:ref";


self::$config['enter']['verify_telegram'] = false;
self::$config['enter']['verify_sms']      = true;
self::$config['enter']['verify_call']     = true;
self::$config['enter']['verify_sendsms']  = false;


?>