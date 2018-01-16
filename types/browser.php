<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications\types;

use lavigor\notifications\functions\subscription;
use Minishlink\WebPush\WebPush;

class browser extends \phpbb\notification\method\base
{
	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\log\log_interface */
	protected $log;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $subscriptions_table;

	/** @var array */
	protected $user_subscriptions = [];

	public function __construct(\phpbb\user_loader $user_loader, \phpbb\user $user, \phpbb\cache\driver\driver_interface $cache, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\log\log_interface $log, $phpbb_root_path, $php_ext, $subscriptions_table)
	{
		$this->user_loader = $user_loader;
		$this->user = $user;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->subscriptions_table = $subscriptions_table;

		parent::__construct($user_loader, $db, $cache, $user, $auth, $config, $phpbb_root_path, $php_ext);
	}

	/**
	 * Get notification method name
	 *
	 * @return string
	 */
	public function get_type()
	{
		return 'lavigor.notifications.notification.method.browser';
	}

	/**
	 * Is the method enabled by default?
	 *
	 * @return bool
	 */
	public function is_enabled_by_default()
	{
		return true;
	}

	/**
	 * Is this method available for the user?
	 * This is checked on the notifications options
	 */
	public function is_available()
	{
		return true;
	}

	/**
	 * Is this method available at all?
	 * This is checked before notifications are sent
	 */
	public function global_available()
	{
		return true;
	}

	public function notify()
	{
		if (!$this->global_available() || empty($this->queue))
		{
			return;
		}

		// Load all users we want to notify (we need their email address)
		$user_ids = $users = array();
		foreach ($this->queue as $notification)
		{
			$user_ids[] = $notification->user_id;
		}

		// We do not send emails to banned users
		if (!function_exists('phpbb_get_banned_user_ids'))
		{
			include($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);
		}
		$banned_users = phpbb_get_banned_user_ids($user_ids);

		// Load all the users we need
		$this->user_loader->load_users($user_ids);

		// Load browsers' data
		$this->get_browsers_data($user_ids);

		$WebPush = new WebPush([
			'VAPID' => [
				'subject'    => 'localhost',
				'publicKey'  => $this->config['push_api_public_key'],
				'privateKey' => $this->config['push_api_private_key'],
			]
		]);

		// Fix for mobile Firefox
		$WebPush->setAutomaticPadding(2000);

		// Time to go through the queue and send emails
		/** @var \phpbb\notification\type\type_interface $notification */
		foreach ($this->queue as $notification)
		{
			$user = $this->user_loader->get_user($notification->user_id);

			if ($user['user_type'] == USER_IGNORE || ($user['user_type'] == USER_INACTIVE && $user['user_inactive_reason'] == INACTIVE_MANUAL) || in_array($notification->user_id, $banned_users))
			{
				continue;
			}

			$display_data = $notification->prepare_for_display();

			$message = html_entity_decode(strip_tags($display_data['FORMATTED_TITLE'] . ' ' . $display_data['REFERENCE'] . $display_data['FORUM'] . $display_data['REASON']), ENT_COMPAT);

			$this->prepare_push_notifications($WebPush, $notification->user_id, [
				'title'     => $this->config['sitename'],
				'message'   => $message,
				'url'       => $this->make_clean_url(str_replace('&amp;', '&', $display_data['URL'])),
				'time'      => $notification->notification_time,
				'badge'		=> $this->config['push_badge_url'],
				'avatar'    => $this->get_avatar_url($this->user->data['user_id']),
			]);
		}

		$results = $WebPush->flush();

		if (is_array($results))
		{
			$this->handle_errors($results);
		}

		// We're done, empty the queue
		$this->empty_queue();
	}

	protected function get_browsers_data(array $user_ids)
	{
		$this->user_subscriptions = [];

		$sql = $this->db->sql_build_query('SELECT', [
			'SELECT' => 's.*',
			'FROM'   => [
				$this->subscriptions_table => 's',
			],
			'WHERE'  => $this->db->sql_in_set('s.user_id', $user_ids),
		]);
		$res = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($res))
		{
			$subscription = new subscription($this->user, $this->db, $this->subscriptions_table);
			$subscription->set_endpoint($row['endpoint'])->set_keys([
				'p256dh' => $row['push_key_p256dh'],
				'auth'   => $row['push_key_auth'],
			]);
			$this->user_subscriptions[$row['user_id']][] = $subscription;
		}
	}

	protected function prepare_push_notifications(WebPush $WebPush, $user_id, array $notification_data)
	{
		if (empty($this->user_subscriptions[$user_id]))
		{
			return;
		}

		$subscriptions = $this->user_subscriptions[$user_id];
		/* @var subscription $subscription */
		foreach ($subscriptions as $subscription)
		{
			$subscription->prepare_notification($WebPush, $notification_data, $this->config['push_notification_ttl']);
		}
	}

	protected function handle_errors(array $results)
	{
		foreach ($results as $res)
		{
			if (!empty($res['expired']))
			{
				$subscription = new subscription($this->user, $this->db, $this->subscriptions_table);
				$subscription
					->set_endpoint($res['endpoint'])
					->remove_by_endpoint()
				;
			}
			else if (isset($res['statusCode']))
			{
				$this->log->add('critical', $this->user->data['user_id'], $this->user->ip, 'LOG_WEB_PUSH_SERVER_ERROR', time(), array($res['statusCode'], $res['message']));
			}
			else if (empty($res['success']))
			{
				$this->log->add('critical', $this->user->data['user_id'], $this->user->ip, 'LOG_WEB_PUSH_GENERAL_ERROR', time(), array($res['message']));
			}
		}
	}

	protected function make_clean_url($url)
	{
		if (strpos($url, $this->phpbb_root_path) !== 0)
		{
			return $url;
		}

		$filesystem = new \Symfony\Component\Filesystem\Filesystem();
		return generate_board_url() . '/' . rtrim($filesystem->makePathRelative($url, $this->phpbb_root_path), '/');
	}

	protected function get_avatar_url($user_id)
	{
		// There's no data field for src - e.g. in Gravatar driver.
		// We need to parse the result HTML string.
		$avatar = $this->user_loader->get_avatar($user_id, true);
		if (!$avatar)
		{
			return generate_board_url() . '/ext/lavigor/notifications/styles/all/template/js/images/no_avatar.gif';
		}

		preg_match('#src="([^"]+)"#im', $avatar, $matches);
		$avatar = $matches[1];

		return $this->make_clean_url($avatar);
	}
}