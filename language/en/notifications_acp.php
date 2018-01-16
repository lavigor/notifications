<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'BROWSER_NOTIFICATION_BADGE_URL'            => 'Absolute URL to the badge image for Push Notifications',
	'BROWSER_NOTIFICATION_DROPDOWN'             => 'Integrate the toggle for browser Push Notifications into the standard dropdown for notifications',
	'BROWSER_NOTIFICATION_DROPDOWN_EXPLAIN'     => 'The toggle for browser Push Notifications on the notification options management page will be shown regardless of this setting.',
	'BROWSER_NOTIFICATION_INTRO'                => 'Ask user whether he/she wants to enable Push Notifications when he/she logs in with a new browser',
	'BROWSER_NOTIFICATION_INTRO_EXPLAIN'        => 'Modal dialog will be shown only once in each browser regardless of login amount with a certain browser.',
	'BROWSER_NOTIFICATION_MAX_BROWSERS'         => 'Maximum allowed number of browsers with enabled Push Notifications per each user',
	'BROWSER_NOTIFICATION_MAX_BROWSERS_EXPLAIN' => 'The user will not be able to turn on Push Notification for the board in any browser if the allowed amount of browsers with enabled Push Notifications has been reached. Enter 0 to disable this setting <em>(not recommended)</em>.',
	'BROWSER_NOTIFICATION_TTL'                  => 'Time To Live for Web Notifications',
	'BROWSER_NOTIFICATION_TTL_EXPLAIN'          => 'Time To Live (TTL) is how long a push message is retained by the push service (e.g. Mozilla) in case the user browser is not yet accessible (e.g. is not connected). You may want to use a very long time for important notifications. The default TTL is 4 weeks. However, if you send multiple nonessential notifications, set a TTL of 0: the push notification will be delivered only if the user is currently connected. For other cases, you should use a minimum of one day if your users have multiple time zones, and if they don’t, several hours will suffice.',
));
