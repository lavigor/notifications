<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\language\language $language, \phpbb\request\request $request)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->template = $template;
		$this->language = $language;
		$this->request = $request;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'                => 'load_language_on_setup',
			'core.page_header'               => 'load_notification_parameters',
			'core.acp_board_config_edit_add' => 'add_config',
		);
	}

	/**
	 * Load language file for notifications
	 *
	 * @param object $event The event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'lavigor/notifications',
			'lang_set' => 'notifications',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Load parameters for notifications
	 *
	 * @param object $event The event object
	 */
	public function load_notification_parameters($event)
	{
		$this->template->assign_vars(array(
			'PUSH_API_PUBLIC_KEY'         => $this->config['push_api_public_key'],
			'S_PUSH_DROPDOWN_INTEGRATION' => $this->config['push_dropdown_integration'],
			'S_PUSH_INTRO_CONFIRMATION'   => $this->config['push_intro_confirmation'],
		));
	}

	/**
	 * Add configuration options
	 *
	 * @param object $event The event object
	 */
	public function add_config($event)
	{
		if ($event['mode'] == 'settings')
		{
			$this->language->add_lang('notifications_acp', 'lavigor/notifications');
			$display_vars = $event['display_vars'];
			/* We add a new legend, but we need to search for the last legend instead of hard-coding */
			$submit_key = array_search('ACP_SUBMIT_CHANGES', $display_vars['vars']);
			$submit_legend_number = substr($submit_key, 6);
			$display_vars['vars']['legend' . $submit_legend_number] = 'BROWSER_NOTIFICATIONS';
			$new_vars = array(
				'push_dropdown_integration'            => array('lang' => 'BROWSER_NOTIFICATION_DROPDOWN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'push_intro_confirmation'              => array('lang' => 'BROWSER_NOTIFICATION_INTRO', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'push_max_browsers'                    => array('lang' => 'BROWSER_NOTIFICATION_MAX_BROWSERS', 'validate' => 'int:0:9999999999', 'type' => 'number:0:9999999999', 'explain' => true),
				'push_notification_ttl'                => array('lang' => 'BROWSER_NOTIFICATION_TTL', 'validate' => 'int:0:9999999999', 'type' => 'number:0:9999999999', 'explain' => true, 'append' => ' ' . $this->language->lang('SECONDS')),
				'push_badge_url'                       => array('lang' => 'BROWSER_NOTIFICATION_BADGE_URL', 'validate' => 'string', 'type' => 'url:40:255', 'explain' => false),
				'legend' . ($submit_legend_number + 1) => 'ACP_SUBMIT_CHANGES',
			);
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $new_vars, array('after' => $submit_key));
			$event['display_vars'] = $display_vars;
		}
	}
}
