/* jshint es3:false, esversion:6 */
/* global pushNotifications, phpbb */
;(function($, window, document) {
	// do stuff here and use $, window and document safely
	// https://www.phpbb.com/community/viewtopic.php?p=13589106#p13589106

	'use strict';

	var notificationButtonHTML = "<div class='push-notify'><span>" + pushNotifications.language.BROWSER_NOTIFICATIONS + "</span><span class='push-toggle'><span></span></span><span class='push-notify-status'></span></div>";
	if (pushNotifications.notificationsPage) {
		$('form#ucp table').before(notificationButtonHTML);
	}
	if (pushNotifications.dropdownIntegration) {
		$('.dropdown-contents .footer')
			.after("<div class='footer'>" + notificationButtonHTML + "</div>");
	}

	var $pushButton = $('.push-notify'), $pushStatus = $('.push-notify-status');
	var isSubscribed = false;
	var swRegistration = null;

	function urlB64ToUint8Array(base64String) {
		var padding = '='.repeat((4 - base64String.length % 4) % 4);
		var base64 = (base64String + padding)
			.replace(/\-/g, '+')
			.replace(/_/g, '/');

		var rawData = window.atob(base64);
		var outputArray = new Uint8Array(rawData.length);

		for (var i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		}
		return outputArray;
	}

	function setButtonState(state) {
		switch (state) {
			case 'enabled':
				$pushStatus.text(pushNotifications.language.ACTIONS.ENABLED);
				$pushButton.removeClass('push-toggle-locked').addClass('push-toggle-enabled')
					.attr('title', pushNotifications.language.DISABLE);
				break;
			case 'disabled':
				$pushStatus.text(pushNotifications.language.ACTIONS.DISABLED);
				$pushButton.removeClass('push-toggle-locked push-toggle-enabled')
					.attr('title', pushNotifications.language.ENABLE);
				break;
			case 'disallowed':
				$pushStatus.text(pushNotifications.language.ACTIONS.DISALLOWED);
				$pushButton.addClass('push-toggle-locked').removeClass('push-toggle-enabled')
					.attr('title', pushNotifications.language.DISALLOWED);
				break;
			case 'unsupported':
				$pushStatus.text(pushNotifications.language.ACTIONS.UNSUPPORTED);
				$pushButton.addClass('push-toggle-locked').removeClass('push-toggle-enabled')
					.attr('title', pushNotifications.language.UNSUPPORTED);
				break;
		}
	}

	function updateBtn() {
		if (Notification.permission === 'denied') {
			setButtonState('disallowed');
			return;
		}
		if (isSubscribed) {
			setButtonState('enabled');
		} else {
			setButtonState('disabled');
		}
	}

	function updateSubscriptionOnServer(subscription, unsubscribe) {
		$.ajax({
			url: pushNotifications.notificationsPath + (unsubscribe ? 'unsubscribe' : 'subscribe'),
			data: JSON.parse(JSON.stringify(subscription)),
			method: 'POST',
			error: function() {
				if (!unsubscribe) { // Unsubscribe in case of an error during subscription.
					unsubscribeUser();
					phpbb.alert(pushNotifications.language.INFORMATION, pushNotifications.language.UPDATE_FAILED);
				}
				// Do not show an error for failed unsubscription - it will be handled on the server side.
			},
			success: function(res) {
				if (!res.id && !unsubscribe) { // Unsubscribe in case of an error during subscription.
					unsubscribeUser();
					phpbb.alert(
						pushNotifications.language.INFORMATION,
						(res.error) ? res.error : pushNotifications.language.UPDATE_FAILED
					);
					return;
				} else if (unsubscribe) {
					return;
				}
				localStorage.pushSubscription = JSON.stringify({
					serverKey: pushNotifications.serverAPIkey,
					serverURL: pushNotifications.notificationsPath,
					subscriptionID: res.id
				});
			},
			cache: false
		});
	}

	function subscribeUser() {
		var applicationServerKey = urlB64ToUint8Array(pushNotifications.serverAPIkey);
		swRegistration.pushManager.subscribe({
			userVisibleOnly: true,
			applicationServerKey: applicationServerKey
		}).then(function(subscription) {
			updateSubscriptionOnServer(subscription);
			isSubscribed = true;
			updateBtn();
		}).catch(function() {
			phpbb.alert(pushNotifications.language.INFORMATION, pushNotifications.language.UPDATE_FAILED);
			updateBtn();
		});
	}

	function unsubscribeUser() {
		swRegistration.pushManager.getSubscription().then(function(subscription) {
			delete localStorage.pushSubscription;
			isSubscribed = false;
			updateBtn();
			if (subscription) {
				updateSubscriptionOnServer(subscription, true);
				return subscription.unsubscribe();
			}
		});
	}

	function subscribe() {
		if (isSubscribed) {
			unsubscribeUser();
		} else {
			subscribeUser();
		}
	}

	function showIntroConfirmation() {
		var introContent = '<form action="{S_CONFIRM_ACTION}" method="post"><h2 style="text-align: center;">' + pushNotifications.language.INTRO + '</h2><fieldset class="submit-buttons" style="padding-top: 15px; font-size: 1.3em;"><input type="button" name="confirm" value="' + pushNotifications.language.INTRO_YES + '" class="button2" style="margin-right: 25px;" />&nbsp;<input type="button" name="cancel" value="' + pushNotifications.language.INTRO_NO + '" class="button2" /></fieldset></form>';
		phpbb.confirm(introContent, function(confirm) {
			if (confirm) {
				$pushButton.first().click();
			}
		});
		localStorage.pushConfirmation = "1";
	}

	if ('serviceWorker' in navigator && 'PushManager' in window) {
		$pushButton.on('click', subscribe);
		navigator.serviceWorker.register(pushNotifications.serviceWorkerPath).then(function(swReg) {
			swRegistration = swReg;

			if (pushNotifications.introConfirmation && !localStorage.pushConfirmation) {
				showIntroConfirmation();
			}

			// Set the initial subscription value
			swRegistration.pushManager.getSubscription().then(function(subscription) {
				isSubscribed = (subscription !== null);

				// It is better to show the real subscription state.
				if (!localStorage.pushSubscription && subscription) {
					isSubscribed = false;
					return subscription.unsubscribe();
				}

				updateBtn();
			});
		});
	} else {
		setButtonState('unsupported');
	}
})(jQuery, window, document);
