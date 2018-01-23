<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * Brazilian Portuguese translation by eunaumtenhoid (c) 2018 [ver 1.2.0] (https://github.com/phpBBTraducoes)
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
	'LAVIGOR_NOTIFICATIONS_NOTIFICATION_METHOD_BROWSER' => 'Navegador',
	'BROWSER_NOTIFICATIONS'                             => 'Notificações do navegador',
	'BROWSER_NOTIFICATIONS_ACTION_ENABLED'              => 'Ligado',
	'BROWSER_NOTIFICATIONS_ACTION_DISABLED'             => 'Desligado',
	'BROWSER_NOTIFICATIONS_ACTION_DISALLOWED'           => 'Não permitido',
	'BROWSER_NOTIFICATIONS_ACTION_UNSUPPORTED'          => 'Não suportado',
	'BROWSER_NOTIFICATIONS_ENABLE'                      => 'Ativar notificações do navegador.',
	'BROWSER_NOTIFICATIONS_DISABLE'                     => 'Desativar notificações do navegador.',
	'BROWSER_NOTIFICATIONS_DISALLOWED'                  => 'As Notificações Web não são permitidas neste navegador. Permita notificações para este site nas configurações do navegador se desejar se inscrever.',
	'BROWSER_NOTIFICATIONS_INTRO'                       => 'Deseja permitir que este portal o avise sobre as respostas?',
	'BROWSER_NOTIFICATIONS_MAX_LIMIT_REACHED'           => 'O número máximo permitido de navegadores com notificações de navegador ativadas  (%s por usuário) foi alcançado. Por favor, desligue as notificações do navegador para este fórum em qualquer outro navegador onde você as ativou se desejar ativar as notificações de navegador neste navegador.',
	'BROWSER_NOTIFICATIONS_UNSUPPORTED'                 => 'As notificações da Web não são suportadas por este navegador.',
	'BROWSER_NOTIFICATIONS_UPDATE_FAILED'               => 'Ocorreu um erro (possivelmente erro de conexão ao entrar em contato com o servidor) durante a assinatura de notificações da Web. Eles foram desativados neste navegador. Por favor verifique sua conexão com a internet e tente novamente. Entre em contato com o administrador do fórum se o problema persistir.',
));
