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

	public function set_endpoint($endpoint)
	{
		$this->endpoint = $endpoint;
		return $this;
	}

	public function set_keys(array $keys)
	{
		$this->push_key_auth = $keys['auth'];
		$this->push_key_p256dh = $keys['p256dh'];
		return $this;
	}

	public function get_id()
	{
		return $this->subscription_id;
	}

	private function build_where_statement()
	{
		return ((isset($this->push_key_auth)) ? '
				push_key_auth   = \'' . $this->db->sql_escape($this->push_key_auth) . '\' AND
				push_key_p256dh = \'' . $this->db->sql_escape($this->push_key_p256dh) . '\' AND
				' : '') . '
				endpoint = \'' . $this->db->sql_escape($this->endpoint) . '\' AND
				user_id         = ' . (int) $this->user->data['user_id'];
	}

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

	public function remove()
	{
		$sql = 'DELETE FROM ' . $this->subscriptions_table . '
				WHERE ' . $this->build_where_statement();
		$this->db->sql_query($sql);
	}


	public function remove_by_endpoint()
	{
		$sql = 'DELETE FROM ' . $this->subscriptions_table . '
				WHERE endpoint = \'' . $this->db->sql_escape($this->endpoint) . '\'';
		$this->db->sql_query($sql);
	}

	public function remove_by_id($id)
	{
		$sql = 'DELETE FROM ' . $this->subscriptions_table . '
				WHERE subscription_id = \'' . (int) $id . '\'';
		$this->db->sql_query($sql);
	}

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
