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
	'LAVIGOR_NOTIFICATIONS_NOTIFICATION_METHOD_BROWSER' => 'Браузерные',
	'BROWSER_NOTIFICATIONS'                             => 'Браузерные уведомления',
	'BROWSER_NOTIFICATIONS_ACTION_ENABLED'              => 'Вкл.',
	'BROWSER_NOTIFICATIONS_ACTION_DISABLED'             => 'Выкл.',
	'BROWSER_NOTIFICATIONS_ACTION_DISALLOWED'           => 'Запрещены',
	'BROWSER_NOTIFICATIONS_ACTION_UNSUPPORTED'          => 'Не поддерживаются',
	'BROWSER_NOTIFICATIONS_ENABLE'                      => 'Включить браузерные уведомления.',
	'BROWSER_NOTIFICATIONS_DISABLE'                     => 'Отключить браузерные уведомления.',
	'BROWSER_NOTIFICATIONS_DISALLOWED'                  => 'Веб-уведомления запрещены в этом браузере. Если вы хотите подписаться на уведомления, снимите запрет для данного сайта в настройках браузера.',
	'BROWSER_NOTIFICATIONS_INTRO'                       => 'Разрешить порталу уведомлять Вас об ответах?',
	'BROWSER_NOTIFICATIONS_MAX_LIMIT_REACHED'           => 'Достигнут предел количества браузеров с включёнными браузерными уведомлениями (%s на одного пользователя). Пожалуйста, отключите браузерные уведомления для данной конференции в любом другом браузере, где вы их уже включили, если вы хотите включить браузерные уведомления в этом браузере.',
	'BROWSER_NOTIFICATIONS_UNSUPPORTED'                 => 'Веб-уведомления не поддерживаются этим браузером.',
	'BROWSER_NOTIFICATIONS_UPDATE_FAILED'               => 'В процессе подписки на веб-уведомления произошла ошибка (возможно, ошибка соединения при попытке связи с сервером). Веб-уведомления были отключены в этом браузере. Пожалуйста, проверьте ваше Интернет-соединение и попробуйте снова. Если проблема повторится, свяжитесь с администратором конференции.',
));
