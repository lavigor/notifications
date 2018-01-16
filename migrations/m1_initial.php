<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications\migrations;

use Minishlink\WebPush\VAPID;

class m1_initial extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\phpbb\db\migration\data\v310\dev'];
	}

	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'push_subscriptions' => [
					'COLUMNS'     => [
						'subscription_id' => ['UINT', null, 'auto_increment'],
						'endpoint'        => ['VCHAR', ''],
						'push_key_auth'   => ['VCHAR', ''],
						'push_key_p256dh' => ['VCHAR', ''],
						'user_id'         => ['UINT', 0],
					],
					'PRIMARY_KEY' => 'subscription_id',
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'push_subscriptions',
			],
		];
	}

	public function update_data()
	{
		if (!isset($this->config['push_api_public_key']) || !isset($this->config['push_api_private_key']))
		{
			$VAPID_keys = $this->get_VAPID_keys();
			return [
				['config.add', ['push_api_private_key', $VAPID_keys['privateKey']]],
				['config.add', ['push_api_public_key', $VAPID_keys['publicKey']]],
			];
		}
		return [];
	}

	public function revert_data()
	{
		// We preserve created VAPID keys.
		return [];
	}

	private function get_VAPID_keys()
	{
		static $VAPID_keys = null;

		if (is_null($VAPID_keys))
		{
			include_once($this->phpbb_root_path . 'ext/lavigor/notifications/vendor/autoload.' . $this->php_ext);
			$VAPID_keys = VAPID::createVapidKeys(); // Should be created only once for the web app.
		}

		return $VAPID_keys;
	}
}
