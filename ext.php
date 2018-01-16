<?php
/**
 *
 * @package       Push Notifications
 * @copyright (c) 2017 - 2018 LavIgor
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace lavigor\notifications;

/**
 * Extension class for custom enable/disable/purge actions
 *
 * NOTE TO EXTENSION DEVELOPERS:
 * Normally it is not necessary to define any functions inside the ext class below.
 * The ext class may contain special (un)installation commands in the methods
 * enable_step(), disable_step() and purge_step(). As it is, these methods are defined
 * in phpbb_extension_base, which this class extends, but you can overwrite them to
 * give special instructions for those cases. This extension must do this because it uses
 * the notifications system, which requires the methods enable_notifications(),
 * disable_notifications() and purge_notifications() be run to properly manage the
 * notifications created by it when enabling, disabling or deleting this extension.
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Overwrite enable_step to enable notifications
	 * before any included migrations are installed.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 */
	function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/** @var \phpbb\db\driver\driver_interface $db */
				$db = $this->container->get('dbal.conn');

				$sql = $db->sql_build_query('SELECT', [
					'SELECT' => 'COUNT(*) as result',
					'FROM'   => [
						USER_NOTIFICATIONS_TABLE => 'n',
					],
					'WHERE'  => "method = 'lavigor.notifications.notification.method.browser'",
				]);
				$res = $db->sql_query($sql);
				$row = $db->sql_fetchfield('result', 0, $res);
				$db->sql_freeresult($res);

				// Check whether the extension had been enabled before.
				if (!empty($row))
				{
					// Restore disabled notifications.
					$sql = 'UPDATE ' . USER_NOTIFICATIONS_TABLE . "
						SET notify = 1
						WHERE method = 'lavigor.notifications.notification.method.browser'";
					$db->sql_query($sql);
				}

				return 'notifications';
			break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
			break;
		}
	}

	/**
	 * Overwrite disable_step to disable notifications
	 * before the extension is disabled.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 */
	function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/** @var \phpbb\db\driver\driver_interface $db */
				$db = $this->container->get('dbal.conn');
				$sql = 'DELETE FROM ' . USER_NOTIFICATIONS_TABLE . "
						WHERE method = 'lavigor.notifications.notification.method.browser'
						AND notify = 0";
				$db->sql_query($sql);

				$sql = 'UPDATE ' . USER_NOTIFICATIONS_TABLE . "
						SET notify = 0
						WHERE method = 'lavigor.notifications.notification.method.browser'
						AND notify = 1";
				$db->sql_query($sql);
				return 'notifications';
			break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}

	/**
	 * Overwrite purge_step to purge notifications before
	 * any included and installed migrations are reverted.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 */
	function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/** @var \phpbb\db\driver\driver_interface $db */
				$db = $this->container->get('dbal.conn');
				$sql = 'DELETE FROM ' . USER_NOTIFICATIONS_TABLE . "
						WHERE method = 'lavigor.notifications.notification.method.browser'";
				$db->sql_query($sql);
				return 'notifications';
			break;
			default:
				// Run parent purge step method
				return parent::purge_step($old_state);
			break;
		}
	}
}
