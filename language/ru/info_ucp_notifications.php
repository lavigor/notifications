<?php
/**
 *
 * @package Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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
	'LAVIGOR_NOTIFICATIONS_NOTIFICATION_METHOD_BROWSER' => 'Браузерные',
));
