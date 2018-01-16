<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications\migrations;

class m3_settings extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\lavigor\notifications\migrations\m2_configuration'];
	}

	public function update_data()
	{
		return [
			['config.add', ['push_dropdown_integration', 1]],
			['config.add', ['push_intro_confirmation', 0]],
			['config.add', ['push_max_browsers', 5]],
		];
	}
}
