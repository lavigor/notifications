<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications\functions;

use Minishlink\WebPush\WebPush;

class subscription
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $subscriptions_table;

	/** @var string */
	protected $endpoint;

	/** @var string */
	protected $push_key_auth;

	/** @var string */
	protected $push_key_p256dh;

	/** @var int */
	protected $subscription_id = 0;

	public function __construct($user, $db, $subscriptions_table)
	{
		$this->user = $user;
		$this->db = $db;
		$this->subscriptions_table = $subscriptions_table;
	}

	/**
	 * Sets subscription's endpoint
	 *
	 * @param string $endpoint New value for endpoint
	 * @return $this
	 */
	public function set_endpoint($endpoint)
	{
		$this->endpoint = $endpoint;
		return $this;
	}

	/**
	 * Sets subscription's keys
	 *
	 * @param array $keys Array with new values to keys 'auth' and 'p256dh'
	 * @return $this
	 */
	public function set_keys(array $keys)
	{
		$this->push_key_auth = $keys['auth'];
		$this->push_key_p256dh = $keys['p256dh'];
		return $this;
	}

	/**
	 * Gets current subscription ID
	 *
	 * @return int
	 */
	public function get_id()
	{
		return $this->subscription_id;
	}

	/**
	 * Builds WHERE statement for SQL Query for current subscription
	 *
	 * @return string
	 */
	private function build_where_statement()
	{
		return ((isset($this->push_key_auth)) ? '
				push_key_auth   = \'' . $this->db->sql_escape($this->push_key_auth) . '\' AND
				push_key_p256dh = \'' . $this->db->sql_escape($this->push_key_p256dh) . '\' AND
				' : '') . '
				endpoint = \'' . $this->db->sql_escape($this->endpoint) . '\' AND
				user_id         = ' . (int) $this->user->data['user_id'];
	}

	/**
	 * Checks whether current subscription exists in the database
	 *
	 * @return bool
	 */
	public function exists()
	{
		$sql = $this->db->sql_build_query('SELECT', [
			'SELECT' => 'COUNT(*) as result',
			'FROM'   => [
				$this->subscriptions_table => 's',
			],
			'WHERE'  => $this->build_where_statement(),
		]);
		$res = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchfield('result', 0, $res);
		$this->db->sql_freeresult($res);

		return !empty($row);
	}

	/**
	 * Submits current subscription to the database if it does not exist there yet
	 */
	public function submit()
	{
		if ($this->exists())
		{
			return;
		}
		$sql = 'INSERT INTO ' . $this->subscriptions_table . $this->db->sql_build_array('INSERT', [
				'endpoint'        => $this->endpoint,
				'push_key_auth'   => $this->push_key_auth,
				'push_key_p256dh' => $this->push_key_p256dh,
				'user_id'         => (int) $this->user->data['user_id'],
			]);
		$this->db->sql_query($sql);
		$this->subscription_id = $this->db->sql_nextid();
	}

	/**
	 * Removes current subscription from the database
	 */
	public function remove()
	{
		$sql = 'DELETE FROM ' . $this->subscriptions_table . '
				WHERE ' . $this->build_where_statement();
		$this->db->sql_query($sql);
	}

	/**
	 * Removes any subscriptions with the specified endpoint from the database
	 */
	public function remove_by_endpoint()
	{
		$sql = 'DELETE FROM ' . $this->subscriptions_table . '
				WHERE endpoint = \'' . $this->db->sql_escape($this->endpoint) . '\'';
		$this->db->sql_query($sql);
	}

	/**
	 * Removes the subscription with the specified ID from the database
	 */
	public function remove_by_id($id)
	{
		$sql = 'DELETE FROM ' . $this->subscriptions_table . '
				WHERE subscription_id = ' . (int) $id;
		$this->db->sql_query($sql);
	}

	/**
	 * Sets up a Push notification to be sent for current subscription
	 *
	 * @param WebPush $WebPush           WebPush object
	 * @param array   $notification_data Array with notification's data
	 * @param int     $ttl               Notification's Time To Live (in seconds)
	 */
	public function prepare_notification(WebPush $WebPush, array $notification_data, $ttl = 0)
	{
		$WebPush->sendNotification(
			$this->endpoint,
			json_encode($notification_data),
			$this->push_key_p256dh,
			$this->push_key_auth,
			false,
			['TTL' => $ttl]
		);
	}
}
