<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications\migrations;

class m2_configuration extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\lavigor\notifications\migrations\m1_initial'];
	}

	public function update_data()
	{
		return [
			['config.add', ['push_notification_ttl', 2419200]],
			['config.add', ['push_badge_url', generate_board_url() . '/ext/lavigor/notifications/styles/all/template/js/images/badge.png']],
		];
	}
}
