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
	'LOG_WEB_PUSH_GENERAL_ERROR'	=> '<strong>Ошибка Web Push</strong><br />» %s',
	'LOG_WEB_PUSH_SERVER_ERROR'		=> '<strong>Ошибка Web Push</strong> с кодом <strong>%1$s</strong><br />» %2$s',
));
