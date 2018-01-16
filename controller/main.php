<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications\controller;

use lavigor\notifications\functions\subscription;
use Symfony\Component\HttpFoundation\JsonResponse;

class main
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\notification\manager */
	protected $manager;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $subscriptions_table;

	public function __construct($user, $db, $config, $helper, $request, $manager, $php_ext, $phpbb_root_path, $subscriptions_table)
	{
		$this->user = $user;
		$this->db = $db;
		$this->config = $config;
		$this->helper = $helper;
		$this->request = $request;
		$this->manager = $manager;
		$this->php_ext = $php_ext;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->subscriptions_table = $subscriptions_table;
	}

	public function subscribe()
	{
		if (!$this->request->is_ajax())
		{
			redirect(append_sid("{$this->phpbb_root_path}index.{$this->php_ext}"));
		}

		if ($this->check_browsers_limit_reached())
		{
			return new JsonResponse([
				'status' => 'error',
				'error'  => $this->user->lang('BROWSER_NOTIFICATIONS_MAX_LIMIT_REACHED', $this->config['push_max_browsers']),
			]);
		}

		$endpoint = $this->request->variable('endpoint', '', true);
		$keys = $this->request->variable('keys', array('' => ''), true);

		$subscription = new subscription($this->user, $this->db, $this->subscriptions_table);

		$old_id = $this->request->variable('subscription_id', 0);
		if ($old_id)
		{
			$subscription->remove_by_id($old_id);
		}

		$subscription
			->set_endpoint($endpoint)
			->set_keys($keys)
			->submit();

		$subscribedToAll = $this->set_default_subscription();

		return new JsonResponse([
			'status'          => 'success',
			'id'              => $subscription->get_id(),
			'subscribedToAll' => $subscribedToAll,
		]);
	}

	public function unsubscribe()
	{
		if (!$this->request->is_ajax())
		{
			redirect(append_sid("{$this->phpbb_root_path}index.{$this->php_ext}"));
		}
		$endpoint = $this->request->variable('endpoint', '', true);
		$keys = $this->request->variable('keys', array('' => ''), true);

		$subscription = new subscription($this->user, $this->db, $this->subscriptions_table);
		$subscription
			->set_endpoint($endpoint)
			->set_keys($keys)
			->remove();

		return new JsonResponse([
			'status' => 'success',
		]);
	}

	protected function check_browsers_limit_reached()
	{
		if ($this->config['push_max_browsers'] < 1)
		{
			return false;
		}

		$sql = $this->db->sql_build_query('SELECT', [
			'SELECT' => 'COUNT(*) as result',
			'FROM'   => [
				$this->subscriptions_table => 's',
			],
			'WHERE'  => 'user_id = ' . (int) $this->user->data['user_id'],
		]);
		$res = $this->db->sql_query($sql);
		$browsers_number = $this->db->sql_fetchfield('result', 0, $res);
		$this->db->sql_freeresult($res);

		return $browsers_number >= $this->config['push_max_browsers'];
	}

	protected function set_default_subscription()
	{
		$sql = $this->db->sql_build_query('SELECT', [
			'SELECT' => 'COUNT(*) as result',
			'FROM'   => [
				USER_NOTIFICATIONS_TABLE => 'n',
			],
			'WHERE'  => "method = 'lavigor.notifications.notification.method.browser'
						AND user_id = " . (int) $this->user->data['user_id'],
		]);
		$res = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchfield('result', 0, $res);
		$this->db->sql_freeresult($res);

		// Check whether the extension had been enabled before.
		if (empty($row))
		{
			// Initialise all browser notifications for every type.
			$sql_insert_array = [];

			foreach ($this->manager->get_subscription_types() as $types)
			{
				foreach ($types as $id => $type)
				{
					$sql_insert_array[] = [
						'method'    => 'lavigor.notifications.notification.method.browser',
						'notify'    => 1,
						'item_type' => $id,
						'item_id'   => 0,
						'user_id'   => (int) $this->user->data['user_id'],
					];
				}
			}

			$this->db->sql_multi_insert(USER_NOTIFICATIONS_TABLE, $sql_insert_array);

			return true;
		}
		return false;
	}
}
