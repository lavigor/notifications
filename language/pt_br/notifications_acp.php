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
	'BROWSER_NOTIFICATION_BADGE_URL'            => 'URL absoluto para a imagem do emblema para Envio de Notificações',
	'BROWSER_NOTIFICATION_DROPDOWN'             => 'Integre a alavanca para o Envio de Notificações no navegador no menu suspenso padrão para notificações',
	'BROWSER_NOTIFICATION_DROPDOWN_EXPLAIN'     => 'A alavanca para o Envio de Notificações no navegador na página de gerenciamento de opções de notificação será exibido independentemente desta configuração.',
	'BROWSER_NOTIFICATION_INTRO'                => 'Pergunte ao usuário se ele/ela quer ativar o Envio de Notificações quando ele/ela faz login com um novo navegador',
	'BROWSER_NOTIFICATION_INTRO_EXPLAIN'        => 'O diálogo formal será mostrado apenas uma vez em cada navegador, independentemente da quantidade de login com um determinado navegador.',
	'BROWSER_NOTIFICATION_MAX_BROWSERS'         => 'Número máximo de navegadores com Envio de Notificações ativadas para cada usuário',
	'BROWSER_NOTIFICATION_MAX_BROWSERS_EXPLAIN' => 'O usuário não poderá ativar o Envio de Notificação para o fórum em qualquer navegador se a quantidade permitida de navegadores com Envio de Notificações ativadas tiver sido alcançada. Digite 0 para desativar esta configuração <em>(não recomendado)</em>.',
	'BROWSER_NOTIFICATION_TTL'                  => 'Time To Live para Notificações Web',
	'BROWSER_NOTIFICATION_TTL_EXPLAIN'          => 'Time To Live (TTL) é por quanto tempo uma mensagem push é retida pelo serviço push (por exemplo, Mozilla) caso o navegador do usuário ainda não esteja acessível (por exemplo, não está conectado). Você pode usar muito tempo para notificações importantes. O TTL padrão é de 4 semanas. No entanto, se você enviar várias notificações não essenciais, defina uma TTL de 0: a notificação será entregue somente se o usuário estiver conectado. Para outros casos, você deve usar um mínimo de um dia se seus usuários tiverem vários fusos horários e, se não o fizerem, várias horas serão suficientes.',
));
