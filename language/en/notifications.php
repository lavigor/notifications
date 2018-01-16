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
	'LAVIGOR_NOTIFICATIONS_NOTIFICATION_METHOD_BROWSER' => 'Browser',
	'BROWSER_NOTIFICATIONS'                             => 'Browser notifications',
	'BROWSER_NOTIFICATIONS_ACTION_ENABLED'              => 'On',
	'BROWSER_NOTIFICATIONS_ACTION_DISABLED'             => 'Off',
	'BROWSER_NOTIFICATIONS_ACTION_DISALLOWED'           => 'Disallowed',
	'BROWSER_NOTIFICATIONS_ACTION_UNSUPPORTED'          => 'Unsupported',
	'BROWSER_NOTIFICATIONS_ENABLE'                      => 'Enable browser notifications.',
	'BROWSER_NOTIFICATIONS_DISABLE'                     => 'Disable browser notifications.',
	'BROWSER_NOTIFICATIONS_DISALLOWED'                  => 'Web Notifications are disallowed in this browser. Allow notifications for this website in browser settings if you want to subscribe.',
	'BROWSER_NOTIFICATIONS_INTRO'                       => 'Do you want to allow this portal to notify you about replies?',
	'BROWSER_NOTIFICATIONS_MAX_LIMIT_REACHED'           => 'The maximum allowed number of browsers with enabled browser notifications (%s per user) has been reached. Please turn off browser notifications for this board in any other browser where you enabled them if you want to enable browser notifications in this browser.',
	'BROWSER_NOTIFICATIONS_UNSUPPORTED'                 => 'Web Notifications are not supported by this browser.',
	'BROWSER_NOTIFICATIONS_UPDATE_FAILED'               => 'There was an error (possibly connection error while contacting the server) during the subscription to Web Notifications. They have been disabled in this browser. Please check your Internet connection and try again. Contact the board administrator if the problem persists.',
));
