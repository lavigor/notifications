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
	 * Check whether or not the extension can be enabled.
	 *
	 * Requirements for versions 1.1.x:
	 * PHP >= 5.6, < 7.2
	 * phpBB >= 3.2.0
	 * PHP extensions: GMP and OpenSSL
	 *
	 * @return bool
	 */
	public function is_enableable()
	{
		return phpbb_version_compare(PHP_VERSION, '5.6.0', '>=') &&
			phpbb_version_compare(PHP_VERSION, '7.2.0', '<') &&
			phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=') &&
			extension_loaded('gmp') && extension_loaded('openssl');
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
				$sql = 'DELETE FROM ' . $this->container->getParameter('tables.user_notifications') . "
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
				$sql = 'DELETE FROM ' . $this->container->getParameter('tables.user_notifications') . "
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
